<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\User;
use App\Http\Middleware\ClientIp;
use App\Logic\V0\Common\VerifyLogic;
use App\Model\V0\User\UserInfoModel;
use App\Model\V0\User\UserModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use JiaLeo\Laravel\Verify\Verify;
use Symfony\Component\HttpKernel\Client;


class UserLogic
{
    public static function Account($data)
    {
        $res = \Session::get('register');
        if($res['keyId']!=$data['keyId']){
            throw new RJsonError('手机号码错误，请重新输入', 'PHONE_ERROR');
        }
        $phone=$res['phone'];
        VerifyLogic::codeSms($phone,$data['code']);
        $password = md5($data['password']);
        $ip = ClientIp::get();
        $account = [
            'phone'=>$phone,
            'password'=>$password,
            'registerIp'=>$ip,
            'registerTime'=>time()
        ];
        $desc=[
            'nickname'=>empty($res['nickname']) ? '' : $res['nickname'],
        ];
        self::addAffair($account,$desc);
    }
    public static function addAffair($account,$desc)
    {
        \DB::beginTransaction();
        try{
            $uid = self::addAccount($account);
            $desc['uid']=$uid;
            self::addAccountDesc($desc);
            \Session::remove('codeImg');
            \Session::put(['uid'=>$uid]);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }
    public static function addAccount($account)
    {
        $model = new UserModel();
        $model->setDataByHumpArray($account)->save();
        return $model->getQueueableId();
    }
    public static function addAccountDesc($accountDesc)
    {
        $model = new UserInfoModel();
        $model->setDataByHumpArray($accountDesc)->save();
        return $model->getQueueableId();
    }

    public static function UserLists($data)
    {
        $model = new UserModel();
        if(!empty($data['phone'])){
            $model = $model->where('easy_user.phone',$data['phone']);
        }
        $JoinLists = $model->leftjoin('easy_user_info', 'easy_user.uid', '=', 'easy_user_info.uid')
            ->select(['*']);
        return $JoinLists->getDdvPageHumpArray(true);
    }

    //获取用户信息需要登录
    public static function getUserByLogin()
    {
        $uid = self::getUid();
        $user = self::getUserById($uid);
        return $user;
    }
    public static function getUserById($uid)
    {
        $model = new UserModel();
        $user = $model->where('easy_user.uid',$uid)
            ->leftjoin('easy_user_info', 'easy_user.uid', '=', 'easy_user_info.uid')
            ->firstHump([
                'easy_user.uid',
                'easy_user.phone',
                'easy_user.state',
                'easy_user.registerTime',
                'easy_user.registerIp',
                'easy_user.loginIp',
                'easy_user_info.headimg',
                'easy_user_info.sex',
                'easy_user_info.nickname',
                'easy_user_info.email',
                'easy_user_info.birthday',
                'easy_user_info.isBindWechat',
            ]);
        return $user;
    }
    //登录
    public static function getUid()
    {
        $uid = LoginLogic::isLogin();
        if(empty($uid)){
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }
        return $uid;
    }
    //根据账号获取用户信息
    public static function getUser($phone)
    {
        $model = new UserModel();
        $user = $model->where('easy_user.phone',$phone)
            ->leftjoin('easy_user_info', 'easy_user.uid', '=', 'easy_user_info.uid')
            ->firstHump([
                'easy_user.*',
                'easy_user_info.*',
            ]);
        return $user;
    }

    //修改
    public static function edit($uid,$data){
        $model = new UserModel();
        $model->where('uid', $uid)->updateByHump($data);
    }
    //修改用户信息
    public static function editUser($data,$uid)
    {
        $model = new UserInfoModel();
        $model->where('uid', $uid)->updateByHump($data);
    }
    //检测账号--存在
    public static function existAccount($data)
    {
        //是否存在
        $user = UserLogic::getUser($data['phone']);
        if(empty($user)){
            //图片验证
            VerifyLogic::imageCode($data['codeImg']);
        }
        if(!empty($user)){
            throw new RJsonError('该账号已存在', 'ACCOUNT_EXIST');
        }
        //创建一个keyId，获取手机号码使用
        $string = VerifyLogic::generateVerifyCode(12);
        $res=[
            'keyId'=>$string,
            'phone'=>$data['phone'],
            'nickname'=>empty($data['nickname']) ? '' : $data['nickname']
        ];
        \Session::put(['register'=>$res]);
        return $string;
    }
    //check用户是否存在--phone
    public static function checkPhone($phone)
    {
        $user = self::getUser($phone);
        if(empty($user)){
            throw new RJsonError('该账号不存在', 'NO_USER');
        }
        return $user;
    }
    //修改密码
    public static function editPassword($data)
    {
        $uid = self::getUid();
        $user = self::getUserById($uid);
        if(md5($data['oldPassword'])!=$user['password']){
            throw new RJsonError('原密码错误', 'OLD_PASSWORD_ERROR');
        }
        if($data['newPassword']!=$data['conPassword']){
            throw new RJsonError('两次密码不一致', 'PASSWORD_ERROR');
        }
        //图片验证
        VerifyLogic::imageCode($data['codeImg']);
        //短信验证
        VerifyLogic::codeSms($user['phone'],$data['codeSms']);
        $password['password']=md5($data['newPassword']);
        self::edit($uid,$password);
        return;
    }
    //修改密码
    public static function resetPassword($data)
    {
        $user = self::checkPhone($data['phone']);
        //短信验证
        VerifyLogic::codeSms($data['phone'],$data['codeSms']);
        $pass = VerifyLogic::generateVerifyCode(8);
        $password['password']=md5(md5($pass));
        self::edit($user['uid'],$password);
        $phone=substr($data['phone'],-4);
        $dataSms=[
            'account'=>$phone,
            'pass'=>$pass,
        ];
        VerifyLogic::sendSmsVerify($data['phone'],$dataSms,$templateCode='SMS_120131114');
        return;
    }

    //修改密码
    public static function editPass($data)
    {
        $user = self::getUserByLogin();
        if($user['password']!=md5($data['passwordOld']))
        {
            throw new RJsonError('原密码错误','PASSWORD_ERROR');
        }
        if($data['passwordNew']!=$data['passwordCon'])
        {
            throw new RJsonError('确认密码错误','PASSWORD_CON_ERROR');
        }
        VerifyLogic::imageCode($data['codeImg']);
        VerifyLogic::codeSms($user['phone'],$data['code']);
        $password['password']=md5($data['passwordNew']);
        self::edit($user['uid'],$password);
    }

    //忘记密码
    public static function forgetPass($data)
    {
        $user =self::getUser($data['phone']);
        if(empty($user))
        {
            throw new RJsonError('账号不存在','PHONE_ERROR');
        }
        if($data['passwordNew']!=$data['passwordCon'])
        {
            throw new RJsonError('确认密码错误','PASSWORD_CON_ERROR');
        }
        VerifyLogic::imageCode($data['codeImg']);
        VerifyLogic::codeSms($user['phone'],$data['code']);
        $password['password'] = md5($data['passwordNew']);
        self::edit($user['uid'],$password);
    }

}

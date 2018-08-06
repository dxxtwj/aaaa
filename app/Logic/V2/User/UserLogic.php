<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\User;
use App\Http\Middleware\ClientIp;
use App\Logic\V2\Common\VerifyLogic;
use App\Model\Subscriber\OauthModel;
use App\Model\Subscriber\UserDescModel;
use App\Model\Subscriber\UserModel;
use \App\Model\UserAccountModel;
use \App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserLogic
{
    public static function Account($data)
    {
        //是否存在
        $user = self::getUser($data['account']);
        if($user){
            throw new RJsonError('该账号已注册', 'NOT_ACCOUNT');
        }
        if($data['type']==2){
            self::verifyPhone($data['account']);
            $user = self::detectPhone($data['account']);
            if(!empty($user)){
                throw new RJsonError('该手机已被绑定过', 'BIND_PHONE');
            }
        }
        if($data['type']==3){
            self::verifyEmail($data['account']);
            $user = self::detectEmail($data['account']);
            if(!empty($user)){
                throw new RJsonError('该邮箱已被绑定过', 'BIND_EMAIL');
            }
        }
        if($data['type']==2){
            VerifyLogic::codeSms($data['account'],$data['code']);
        }
        if($data['type']==3){
            VerifyLogic::EmailCode($data['account'],$data['code']);
        }
        //验证图片
        //VerifyLogic::imageCode($data['codeImg']);
        $password = md5($data['password']);
        $account = [
            'account'=>$data['account'],
            'password'=>$password,
            'type'=>$data['type'],//手机
            'registerTime'=>time()
        ];
        self::addAffair($account);
    }
    public static function addAffair($account)
    {
        \DB::beginTransaction();
        try{
            $uid = self::addAccount($account);
            $user=[
                'uid'=>$uid
            ];
            self::addAccountDesc($user);
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
        $model->setSiteId()->setDataByHumpArray($account)->save();
        return $model->getQueueableId();
    }
    public static function addAccountDesc($accountDesc)
    {
        $model = new UserDescModel();
        $model->setDataByHumpArray($accountDesc)->save();
        return $model->getQueueableId();
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
    public static function getUser($userAccount)
    {
        $user = UserModel::whereSiteId()
            ->where('user.account',$userAccount)
            ->leftjoin('user_description', 'user.uid', '=', 'user_description.uid')
            ->firstHump([
                'user.*',
                'user_description.*',
            ]);
        return $user;
    }
    //修改
    public static function edit($uid,$data){
        UserModel::where('uid', $uid)->updateByHump($data);
    }
    //修改用户信息
    public static function editUser($data,$uid)
    {
        UserDescModel::where('uid', $uid)->updateByHump($data);
    }
    //修改密码
    public static function editPassword($data)
    {
        $uid = self::getUid();
        $user = self::getUserById($uid);
        if(md5($data['oldPassword'])!=$user['password']){
            throw new RJsonError('原密码错误', 'OLD_PASSWORD_ERROR');
        }
        if(md5($data['newPassword'])!=md5($data['conPassword'])){
            throw new RJsonError('两次密码不一致', 'PASSWORD_ERROR');
        }
        $password['password']=md5($data['newPassword']);
        self::edit($uid,$password);
        return;
    }
    //忘记密码验证码验证
    public static function forgetVerify($data)
    {
        self::verifyAccount($data['account'],$data['type']);
        //是否存在
        $user = self::getUser($data['account']);
        if(empty($user)){
            throw new RJsonError('该账号不存在', 'NOT_ACCOUNT');
        }
        //验证图片
        VerifyLogic::imageCode($data['codeImg']);
        if($data['type']==2){
            //验证短信
            VerifyLogic::codeSms($data['account'],$data['code']);
        }
        if($data['type']==3){
            VerifyLogic::EmailCode($data['account'],$data['code']);
        }
    }
    //重设密码
    public static function forgetPassword($data)
    {
        self::verifyAccount($data['account'],$data['type']);
        //是否绑定
        if($data['type']==2){
            $user = self::detectPhone($data['account']);
            if(empty($user)){
                throw new RJsonError('该手机号码还没有被绑定', 'NOT_BIND_PHONE');
            }
        }
        if($data['type']==3){
            $user = self::detectEmail($data['account']);
            if(empty($user)){
                throw new RJsonError('该邮箱还没有被绑定', 'NOT_BIND_EMAIL');
            }
        }
        if($data['newPassword']!=$data['conPassword']){
            throw new RJsonError('两次密码不一致', 'PASSWORD_ERROR');
        }
        $password['password'] = md5($data['conPassword']);
        //重置密码
        self::edit($user['uid'],$password);

    }
    //验证账号类型
    public static function verifyAccount($account,$type)
    {
        if($type==1){
            if(!preg_match('/^[0-9a-zA-Z]{4,16}$/',$account)){
                throw new RJsonError('账号格式不对', 'ACCOUNT_ERROR');
            }
        }
        if($type==2){
            self::verifyPhone($account);
        }
        if($type==3){
            self::verifyEmail($account);
        }
    }
    public static function verifyPhone($phone)
    {
        if(!preg_match('^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\\d{8}$^',$phone)){
            throw new RJsonError('手机格式不对', 'PHONE_ERROR');
        }
    }
    public static function verifyEmail($email)
    {
        if(!preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ',$email)){
            throw new RJsonError('邮箱格式不对', 'EMAIL_ERROR');
        }
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
        $user = UserModel::whereSiteId()
            ->where('user.uid',$uid)
            ->leftjoin('user_description', 'user.uid', '=', 'user_description.uid')
            ->firstHump([
                'user.*',
                'user_description.*',
            ]);
        return $user;
    }
    //绑定手机
    public static function bindPhone($data)
    {
        $uid = self::getUid();
        self::verifyPhone($data['userPhone']);
        //验证图片
        VerifyLogic::imageCode($data['codeImg']);
        //验证短信
        VerifyLogic::codeSms($data['userPhone'],$data['code']);
        //看看这手机有没有被注册过
        $userInfo = self::getUser($data['userPhone']);
        if(!empty($userInfo)){
            if($uid!=$userInfo['uid']){
                throw new RJsonError('该手机已被注册过，不能再绑定', 'USER_PHONE');
            }
        }
        //看看这个手机在这个站有没有被绑定过
        $user = self::detectPhone($data['userPhone']);
        if(!empty($user)){
            throw new RJsonError('该手机已被绑定过', 'BIND_PHONE');
        }
        $desc['userPhone']=$data['userPhone'];
        $main['isBindPhone']=1;
        self::edit($uid,$main);
        self::editUser($desc,$uid);
        \Session::remove('codeSms');
        \Session::remove('phone');
        return;
    }
    //检测手机有没有别绑定过
    public static function detectPhone($phone)
    {
        $user = UserModel::whereSiteId()
            ->where('user_description.user_phone',$phone)
            ->leftjoin('user_description', 'user.uid', '=', 'user_description.uid')
            ->firstHump([
                'user.*',
                'user_description.*',
            ]);
        return $user;
    }
    //绑定邮箱
    public static function bindEmail($data)
    {
        $uid = self::getUid();
        self::verifyEmail($data['userEmail']);
        //验证图片
        VerifyLogic::imageCode($data['codeImg']);
        //邮箱验证
        VerifyLogic::EmailCode($data['userEmail'],$data['code']);
        //看看这手机有没有被注册过
        $userInfo = self::getUser($data['userEmail']);
        if(!empty($userInfo)){
            if($uid!=$userInfo['uid']){
                throw new RJsonError('该邮箱已被注册过，不能再绑定', 'USER_EMAIL');
            }
        }
        //查看邮箱有没有被绑定过
        $user = self::detectEmail($data['userEmail']);
        if(!empty($user)){
            throw new RJsonError('该邮箱已被绑定过', 'BIND_EMAIL');
        }
        $desc['userEmail']=$data['userEmail'];
        $main['isBindEmail']=1;
        self::edit($uid,$main);
        self::editUser($desc,$uid);
        \Session::remove('emailCode');
        \Session::remove('email');
        return;
    }
    //检测邮箱有没有别绑定过
    public static function detectEmail($email)
    {
        $user = UserModel::whereSiteId()
            ->where('user_description.user_email',$email)
            ->leftjoin('user_description', 'user.uid', '=', 'user_description.uid')
            ->firstHump([
                'user.*',
                'user_description.*',
            ]);
        return $user;
    }

    //获取用户列表
    public static function getUserLists($data)
    {
        if (isset($data['userNickname'])) {
            $Nickname = '%' . $data['userNickname'] . '%';
        } else {
            $Nickname = '%';
        }
        $user = UserModel::whereSiteId()
            ->where('user_description.user_nickname', 'like', $Nickname)
            ->leftjoin('user_description', 'user.uid', '=', 'user_description.uid')
            ->select([
                'user.*',
                'user_description.*',
            ]);
        $res = $user->getDdvPageHumpArray(true);
        return $res;
    }

    //修改微信配置
    public static function editConfig()
    {

        \Config::set('wechat.mp',
            array(
                'debug' => 'true',
                'logcallback' => 'wechat_error_log',
                'token'=>'5a5f6c4b6e1cf4ddcb679111ef33e35c', //填写你设定的key
                'encodingaeskey'=>'6JRkAXGEFRRSAoIJWMWS7xcXNbe5zcoo89YF0Bt7kwu', //填写加密用的EncodingAESKey
                'appid' => 'wx8956bb1b3181f683',
                'appsecret' => '02f407f9d122ab51aabff431ccc17558'
            ));
    }

    //检测账号--不存在
    public static function checkAccount($data)
    {
        //是否存在
        $user = UserLogic::getUser($data['account']);
        if(empty($user)){
            if($data['type']==2){
                $user = UserLogic::detectPhone($data['account']);
            }
            if($data['type']==3){
                $user = UserLogic::detectEmail($data['account']);
            }
            if(empty($user)){
                throw new RJsonError('没有该账号', 'NOT_ACCOUNT');
            }
        }
        return $user;
    }
    //检测账号--存在
    public static function existAccount($data)
    {
        //是否存在
        $user = UserLogic::getUser($data['account']);
        if(empty($user)){
            if($data['type']==2){
                $user = UserLogic::detectPhone($data['account']);
            }
            if($data['type']==3){
                $user = UserLogic::detectEmail($data['account']);
            }
        }
        if(!empty($user)){
            throw new RJsonError('该账号已存在', 'ACCOUNT_EXIST');
        }
        return;
    }

    //检测账号--不存在--微信、微博、qq
    public static function checkAccountByWechat($data)
    {
        //是否存在
        $user = UserLogic::getUser($data['account']);
        if(empty($user)){
            if($data['type']==2){
                $user = UserLogic::detectPhone($data['account']);
            }
            if($data['type']==3){
                $user = UserLogic::detectEmail($data['account']);
            }
        }
        return $user;
    }
    //微信添加使用
    public static function addAffairByWechat($account,$userDesc,$user_obj,$res)
    {
        \DB::beginTransaction();
        try{
            $uid = self::addAccount($account);
            $userDesc['uid']=$uid;
            self::addAccountDesc($userDesc);
            WechatLogic::addWechat($user_obj,$res);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return $uid;
    }
    //微信添加使用
    public static function addAffairWechat($account,$userDesc)
    {
        \DB::beginTransaction();
        try{
            $uid = self::addAccount($account);
            $userDesc['uid']=$uid;
            self::addAccountDesc($userDesc);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return $uid;
    }

    //微博授权信息
    public static function getMessageByUoaid($data)
    {
        $uoaid = OauthLogic::getSessionUoaid();
        $auth = WechatLogic::getWechatByUoaid($uoaid);
        $res = json_decode($auth['jsondata'],true);
        if($data['authType']=='weibo'){
            $info = $res['usersShow'];
            $userInfo['nickname'] = $info['screen_name']??$info['name']??'';
            $userInfo['avatar'] = $info['avatar_hd']??$info['avatar_large']??$info['profile_image_url']??'';
            $userInfo['sex'] = empty($info['gender'])?0:($info['gender']=='m'?1:($info['gender']=='f'?2:0));
        }
        if($data['authType']=='qq'){
            $info = $res['scopeGetUserInfo'];
            $userInfo['nickname'] = $info['nickname']??$info['nickname']??'';
            $userInfo['avatar'] = $info['figureurl_qq_2']??'';
            $userInfo['sex'] = empty($info['gender'])?0:($info['gender']=='男'?1:($info['gender']=='女'?2:0));
        }
        \Session::put(['authInfo'=>$userInfo]);
        return $userInfo;
    }

}

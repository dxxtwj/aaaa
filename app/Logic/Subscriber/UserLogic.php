<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Subscriber;

use App\Model\Subscriber\UserModel;
use App\Model\Subscriber\UserDescModel;
use App\Model\Subscriber\UserSecurityModel;
use App\Model\Subscriber\UserSecurityAnswerModel;
use App\User;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class UserLogic
{
    //public $phone = '';
    //全部
    public static function addRegister ($data=[])
    {
        $user = self::getUser($data['account']);
        $phone='';
        $isBindPhone=0;
        if(isset($user))
        {
            throw new RJsonError('该账号已存在，请换一个', 'USER_EXIST_ERROR');
        }
        if($data['type']==1)
        {
            self::accountName($data['account']);
        }
        if($data['type']==2)
        {
            self::accountPhone($data['account'],$data['code']);
            $defaultPass=md5(md5($data['account']));//密码为手机号码
            $pass='14e1b600b1fd579f47433b88e8d85291';//123456
            $phone=$data['account'];
            $isBindPhone=1;
        }
        if($data['type']==3)
        {
            self::accountEmail($data['account']);
        }
        if(!empty($data['password'])){
            $password1=md5($data['password']);//前端也要加密
        }
        $password=empty($password1) ? $pass : $password1;
        //主
        $mian=[
            'account'=>$data['account'],
            'password'=>$password,
            'type'=>$data['type'],
            'isSecurity'=>empty($data['isSecurity']) ? 0 : $data['isSecurity'],
            'registerTime'=>time(),
            'isOn'=>1,
            'isBindPhone'=>$isBindPhone
        ];
        $uid=self::addUser($mian);
        //详
        $desc=[
            'uid' => $uid,
            'userNickname' => empty($data['userNickname']) ? $data['account'] : $data['userNickname'],
            'userPhone'=>empty($phone) ? '' : $phone
        ];
        self::addUserDesc($desc);
        //密保
        if(!empty($data['isSecurity']) && $data['isSecurity']==1){
            self::security($data['isSecurity'],$uid);
        }
    }

    //账号
    public static function accountName($accountName){
        if(!preg_match('/^[0-9a-zA-Z]{4,16}$/',$accountName)){
            throw new RJsonError('账号格式不对', 'ACCOUNT_ERROR');
        }
    }
    //手机
    public static function accountPhone($accountPhone,$accountCode){
        $phone = \Session::get('phone',null);
        if(!preg_match('^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\\d{8}$',$accountPhone)){
            throw new RJsonError('手机格式不对', 'PHONE_ERROR');
        }
        if(empty($phone)){
            throw new RJsonError('请获取验证码','NO_PHONE_ERROR');
        }
        if($phone!=$accountPhone){
            throw new RJsonError('电话号码和接收验证码的电话不一致','PHONE_ERROR');
        }
        $code = \Session::get('code',null);
        if(empty($code)){
            throw new RJsonError('验证码已过期','NO_CODE_ERROR');
        }
        if($code!=$accountCode){
            throw new RJsonError('验证码错误','CODE_ERROR');
        }
    }
    //邮箱
    public static function accountEmail($email)
    {
        if(!preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ',$email))
        {
            throw new RJsonError('邮箱格式不对', 'EMAIL_ERROR');
        }
    }

    //主表
    public static function addUser($data=[])
    {
        $model = new UserModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //详情
    public static function addUserDesc($data=[])
    {
        $model = new UserDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //密保
    public static function security($data=[],$uid){
        foreach ($data['security'] as $value){
            $security=[
                'uid'=>$uid,
                'securityCateId'=>$value['securityCateId'],
                'securityTitle'=>$value['securityTitle']
            ];
            self::addSecurity($security);
        }
    }

    //答案
    public static function addSecurity($data=[])
    {
        $model = new UserSecurityAnswerModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model;
    }
    /*//获取列表
    public static function getAnswerLists($uid)
    {
        $answer = UserSecurityAnswerModel::whereSiteId()
            ->where('uid',$uid)
            ->getHumpArray(['*']);
        return $answer;
    }*/
    //检测回答的问题
    public static function getSecurityAnswer($uid,$securityCateId,$securityTitle)
    {
        $answer = UserSecurityAnswerModel::whereSiteId()
            ->where('uid',$uid)
            ->where('security_cate_id',$securityCateId)
            ->where('security_title',$securityTitle)
            ->firstHunmp(['*']);
        return $answer;
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

    //测试-没用
    public static function getUsers($account)
    {
        $user = UserModel::whereSiteId()->where('account',$account)->firstHump(['*']);
        if(!empty($user)){
            $user->desc=$user->Desc()->firstHumpArray();
        }
        return $user;
    }

    //根据id获取用户信息
    public static function getinfo($uid)
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

    //重置密码
    public static function resetPassword($uid,$account,$data){
        UserModel::where('uid', $uid)->where('account',$account)->updateByHump($data);
    }


}
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
use App\Logic\Subscriber\UserLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LoginLogic
{
    //全部
    public static function Login ($data=[])
    {
        $user = UserLogic::getUser($data['account']);
        if(empty($user)){
            throw new RJsonError('账号错误,或者不存在', 'USER_ERROR');
        }
        //账号，如果绑定手机，可以手机登录
        if($data['type']==1){
            self::account($data);
        }
        //手机登录
        if($data['type']==2){
            self::accountPhone();
        }else{
            if($user->password!=md5($data['password'])){
                throw new RJsonError('密码错误', 'USER_PASSWORD_ERROR');
            }
        }
        \Session::put('uid', $user->uid);
        return $data=[
            'userNickname'=>empty($user->userNickname) ? $user->account : $user->userNickname,
            'uid'=>$user->uid
        ];
    }

    //type==1,账号登录，如果绑定了手机，可以手机登录
    public static function account($data)
    {
        if(isset($data['userPhone'])){
            $phone = \Session::get('phone',null);
            if(empty($phone)){
                throw new RJsonError('请获取验证码', 'NO_PHONE_ERROR');
            }
            if($phone!=$data['userPhone']){
                throw new RJsonError('输入的手机号码跟接收短信的手机号码不一致', 'PHONE_ERROR');
            }
            $code = \Session::get('code',null);
            if(empty($code)){
                throw new RJsonError('验证码已过期，请获取验证码', 'NO_CODE_ERROR');
            }
            if($code!=$data['code']){
                throw new RJsonError('验证码不正确', 'CODE_ERROR');
            }
        }
    }

    //type==2、手机登录
    public static function accountPhone($data)
    {
        $phone = \Session::get('phone',null);
        if(empty($phone)){
            throw new RJsonError('请获取验证码', 'NO_PHONE_ERROR');
        }
        if($phone!=$data['account']){
            throw new RJsonError('输入的手机号码跟接收短信的手机号码不一致', 'PHONE_ERROR');
        }
        $code = \Session::get('code',null);
        if(empty($code)){
            throw new RJsonError('验证码已过期，请获取验证码', 'NO_CODE_ERROR');
        }
        if($code!=$data['code']){
            throw new RJsonError('验证码不正确', 'CODE_ERROR');
        }
    }

    public static function isLogin(){
        return !empty(self::getUid());

    }
    public static function getUid(){
        return session('uid', null);
    }

    //退出登录
    public static function logout()
    {
        \Session::forget('uid');
        return true;
    }

    //查是否有这个账号
    public static function getAccount($account)
    {
        $user = UserLogic::getUser($account);
        if(empty($user)){
            throw new RJsonError('账号错误,或者不存在', 'USER_ERROR');
        }
        return;
    }

    //忘记密码
    public static function securityAnswer($data)
    {
        //$user = UserLogic::getUser($data['account']);
        if($data['type']==1){
            self::forgetAccount($data);
        }
        if($data['type']==2){
            self::forgetPhone($data);
        }
        if($data['type']==3){
            self::forgetEmail($data);
        }
        return $arr=[
            'uid'=>empty($user['uid']) ? '' : $user['uid'],
        ];
    }

    //账号忘记密码
    public static function forgetAccount($data){
        $user = UserLogic::getUser($data['account']);
        /*//查问题回答表
            $answer = UserLogic::getAnswerLists($user['uid']);*/
        foreach ($data['security'] as $item) {
            //var_dump('111');
            $res = UserLogic::getSecurityAnswer($user['uid'],$item['securityCateId'],$item['securityTitle']);
            //var_dump($res);
            if(empty($res)){
                throw new RJsonError('不正确', 'ANSWER_ERROR');
            }
        }
    }

    //手机忘记密码
    public static function forgetPhone($data){
        $phone = \Session::get('phone',null);
        if(empty($phone)){
            throw new RJsonError('请获取验证码', 'NO_PHONE_ERROR');
        }
        if($phone!=$data['account']){
            throw new RJsonError('输入的手机号码跟获取验证码的手机号码不一致', 'PHONE_ERROR');
        }
        $code = \Session::get('code',null);
        if(empty($code)){
            throw new RJsonError('验证码已过期', 'ON_CODE_ERROR');
        }
        if($code!=$data['code']){
            throw new RJsonError('验证码错误', 'CODE_ERROR');
        }
    }
    //邮箱忘记密码
    public static function forgetEmail($data){
        $email = \Session::get('email',null);
        if(empty($email)){
            throw new RJsonError('请获取验证码', 'NO_EMAIL_ERROR');
        }
        if($email!=$data['account']){
            throw new RJsonError('输入的邮箱跟获取验证码的邮箱不一致', 'EMAIL_ERROR');
        }
        $code = \Session::get('code',null);
        if(empty($code)){
            throw new RJsonError('验证码已过期', 'ON_CODE_ERROR');
        }
        if($code!=$data['code']){
            throw new RJsonError('验证码错误', 'CODE_ERROR');
        }
    }

    //重置密码
    public static function resetPassword($data)
    {
        $user = UserLogic::getUser($data['account']);
        if(empty($user)){
            throw new RJsonError('账号错误,或者不存在', 'USER_ERROR');
        }
        if($data['newsPassword']!=$data['confirmPassword']){
            throw new RJsonError('确认密码错误', 'CONFIRM_ERROR');
        }
        $newPassword=[
            'password'=>md5($data['newsPassword'])
        ];
        UserLogic::resetPassword($data['uid'],$data['account'],$newPassword);
    }


}
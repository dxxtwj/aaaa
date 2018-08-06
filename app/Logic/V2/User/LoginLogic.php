<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\User;
use App\Logic\V2\Common\VerifyLogic;
use App\Model\Subscriber\UserDescModel;
use App\Model\Subscriber\UserModel;
use \App\Model\UserAccountModel;
use \App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class LoginLogic
{
    public static function login($data)
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
                throw new RJsonError('没有该账号', 'NOT_ACCOUNT_ACCOUNT');
            }
        }
        /*if($data['type']==2){
            //验证短信
            VerifyLogic::codeSms($data['account'],$data['code']);
        }
        if($data['type']==3){
            //验证邮箱
            VerifyLogic::EmailCode($data['account'],$data['code']);
        }*/
        if(md5($data['password'])!=$user['password']){
            throw new RJsonError('密码错误', 'NOT_PASSWORD_ACCOUNT');
        }
        \Session::put(['uid'=>$user['uid']]);
        return;
    }

    //登录状态
    public static function isLogin()
    {
        //\Session::put(['uid'=>1]);
        $uid = \Session::get('uid');//
        return $uid;
    }

    //退出登录
    public static function logout()
    {
        \Session::remove('uid');
        return true;
    }

    /*
     *
     */
    public static function setLogin($uid, $clientIp)
    {
        \Session::put('uid', $uid);
        return;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\User;
use App\Logic\Common\VerifyLogic;
use \App\Model\UserAccountModel;
use \App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class LoginLogic
{
    /**
     * 登录逻辑
     */
    public static function accountLogin($account, $password, $clientIp)
    {
        $userAccount = AccountLogic::getOneByAccount($account, [
            'uaid','uid','state','type','loginNum'
        ]);
        if ($userAccount->state<1){
            throw new RJsonError('账号异常', 'ACCOUNT_STATUS_ERROR');
        }

        $userBase = UserBaseModel::where('uid', $userAccount->uid)->firstHump([
            'state','loginNum','loginTime','loginIp','password'
        ]);

        // 判断密码是否正确
        if ($userBase&&$password!==$userBase->password){
            throw new RJsonError('账号或密码错误', 'USER_OR_PASSWORD_ERROR');
        }

        return self::setLogin($userAccount->uid, $clientIp, $userAccount->type, $userBase);

    }
    public static function smsLogin($phone, $clientIp)
    {
        $res = AccountLogic::getOneByPhone($phone);
        if ($res->state<1){
            throw new RJsonError('账号异常', 'ACCOUNT_STATUS_ERROR');
        }
        return self::setLogin($res->uid, $clientIp, 2);
    }
    public static function setLogin($uid, $clientIp, $accountType, $userBase = null)
    {

        if (empty($userBase)){
            $userBase = UserBaseModel::where('uid', $uid)->firstHump([
                'state','loginNum','loginTime','loginIp','password'
            ]);
        }
        if (!$userBase){
            throw new RJsonError('账号不存在', 'NOT_FIND_ACCOUNT');
        }
        if ($userBase->state<1){
            throw new RJsonError('账号异常', 'ACCOUNT_STATUS_ERROR');
        }
        // 更新最后登录信息
        UserAccountModel::where('uid', $uid)->updateByHump([
            'lastLoginIp'=>$clientIp
        ]);
        // 更新最后登录信息
        UserBaseModel::where('uid', $uid)->updateByHump([
            'loginNum'=> intval($userBase->loginNum)+1,
            'loginTime'=>time(),
            'lastLoginTime'=>$userBase->loginTime,
            'loginIp'=>$clientIp,
            'lastLoginIp'=>$userBase->loginIp
        ]);
        \Session::put('uid', $uid);
        return [
            'loginAccountType' => $accountType
        ];
    }
    public static function logout (){
        \Session::remove('uid');
        return true;
    }
    public static function isLogin(){
        return !empty(self::getLoginUid());
//        return true;
    }
    public static function getLoginUid(){
        return session('uid', null);
//        return true;
    }

}

<?php

namespace App\Logic\V0\Admin;

use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class OurLoginLogic
{
    public static function login($administratorName, $administratorPassword)
    {
        $admin=AdministratorLogic::getAdministrator($administratorName);
        if(!$admin){
            throw new RJsonError('用户不存在', 'USER_ERROR');
        }
//        var_dump($admin->administratorPassword);
        if($admin->administratorPassword!=md5($administratorPassword)){
            throw new RJsonError('密码错误', 'PASSWORD_ERROR');
        }
//        var_dump($admin->administratorId);
        \Session::put('administratorId', $admin->administratorId);
        return [
            'administratorName' => $admin->administratorName,
            'administratorId' => $admin->administratorId
        ];
    }

    public static function getLoginId(){
        $administratorId = session('administratorId');
        if (!$administratorId) {
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }
        return $administratorId;
    }




    //登录状态
//    public static function isLogin()
//    {
//        if (!empty(self::getAdminId())) {
//            $data['is_login'] = true;
//        } else {
//            $data['is_login'] = false;
//        }
//        return $data;
//    }

    public static function logout()
    {
        \Session::forget('administratorId');
        return true;
    }

    public static function isLogin(){
        return !empty(self::getAdministratorId());
        return false;
    }
    public static function getAdministratorId(){
        return session('administratorId', null);
        return true;
    }







}
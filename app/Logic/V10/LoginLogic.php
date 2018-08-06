<?php

namespace App\Logic\V10;

use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LoginLogic
{
    public static function login($name, $password)
    {
        $admin=AdminLogic::getAdmin($name);
        if(!$admin){
            throw new RJsonError('用户不存在', 'USER_ERROR');
        }
        if($admin['password']!=md5($password)){
            throw new RJsonError('密码错误', 'PASSWORD_ERROR');
        }
        \Session::put('adminId', $admin['adminId']);
        return [
            'name' => $admin['name'],
            'adminId' => $admin['adminId']
        ];
    }


    //获取用户名称
    public static function getAdmin(){
        $adminId = \Session::get('adminId');
        $admin=AdminLogic::getAdminById($adminId);
        return $admin;
    }

    public static function getLoginId()
    {
        $adminId = \Session::get('adminId');
        if(empty($adminId)){
            throw new RJsonError('未登录', 'NO_LOGIN');
        }
        return $adminId;
    }

    public static function logout()
    {
        \Session::forget('adminId');
        return true;
    }

    public static function isLogin(){
        return !empty(self::getAdminId());
        return false;

    }
    public static function getAdminId(){
       return session('adminId', null);
        return true;
    }







}
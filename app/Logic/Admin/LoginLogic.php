<?php

namespace App\Logic\Admin;

use App\Logic\AdminLogic;
use App\Model\Admin\AdminModel;
use App\Model\Admin\AdminDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LoginLogic
{
    public static function login($adminName, $adminPassword)
    {
        $admin=AdminLogic::getAdmin($adminName);
        if(!$admin){
            throw new RJsonError('用户不存在', 'USER_ERROR');
        }
//        var_dump($admin->adminPassword);
        if($admin->adminPassword!=md5($adminPassword)){
            throw new RJsonError('密码错误', 'PASSWORD_ERROR');
        }
        \Session::put('adminId', $admin->adminId);
        return [
            'adminName' => $admin->adminName,
            'adminId' => $admin->adminId
        ];
    }


    //获取用户名称
    public static function getAdmin(){
        $adminId = \Session::get('adminId');
        $admin=AdminLogic::getAdminLogin($adminId);
        return $admin;
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
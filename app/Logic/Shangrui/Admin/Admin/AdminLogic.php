<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\Admin;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Admin\AdminModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class AdminLogic extends ShoppingLogic
{
    // 后台登录
    public static function login($data = array()){
        $adminModel = new AdminModel();
        $admin = $adminModel->where('admin_name','admin')->firstHumpArray();

        if ($data['adminPassword'] != $admin['adminPassword'] || empty($admin)){
            throw new RJsonError('账号或者密码错误','ADMIN_ERROR');
        }

        \Session::put('adminId', $admin['adminId']);

    }
    // 修改后台管理密码
    public static function editAdmin($data = array()){

        $adminModel = new AdminModel();
        $pass['admin_password'] = $data['adminPassword'];

        $adminModel->where('admin_id',$data['adminId'])->updateByHump($pass);

    }


}
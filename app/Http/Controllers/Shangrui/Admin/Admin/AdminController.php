<?php

namespace App\Http\Controllers\Shangrui\Admin\Admin;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Admin\Admin\AdminLogic;
use App\Model\V10\AdminModel;


class AdminController extends Controller
{
    // 后台管理员登录
    public function login(){
        $this->verify(
            [
                'adminName' => '', //用户名
                'adminPassword' => '', //密码

            ]
            ,'POST');
        $res = AdminLogic::login($this->verifyData);
        return $res;
    }
    // 后台修改密码
    public function editAdmin(){
        $this->verify(
            [
                'adminId' => '',
                'adminPassword' => '',
            ]
            ,'POST');
        $res = AdminLogic::editAdmin($this->verifyData);
    }
}
<?php

namespace App\Http\Controllers\Shopping\Admin\Admin;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Admin\AdminLogic;

class AdminController extends Controller
{
    /*
     * 后台登录
     */
    public function login() {
        $this->verify(
            [
                'adminName' => '',//管理员账号
                'adminPassword' => '',//管理员密码
            ]
            , 'POST');
        $res = AdminLogic::login($this->verifyData);
        return $res;
    }

    /*
     * 创建管理员
     */
    public function addAdmin() {
        $this->verify(
            [
                'adminName' => '',//管理员账号
                'adminPassword' => '',//管理密码
                'adminPhone' => 'no_required',//管理员手机号
            ]
            , 'POST');
        $res = AdminLogic::addAdmin($this->verifyData);
        return $res;
    }


}

<?php

namespace App\Http\Controllers\V0\Admin\Admin;

use App\Logic\V0\Admin\AdminLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AdminController extends Controller
{
    //添加
    public function AddAdmin()
    {
        $this->verify(
            [
                'siteId' =>'',
                'adminName' => '',//语言名称
                'adminPassword'=>''
            ]
            , 'POST');
        AdminLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getAdminLists(){
        $this->verify(
            [
                'siteId' =>'no_required',
            ]
            , 'GET');
        $res = AdminLogic::getAdminList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getAdminOne(){
        $this->verify(
            [
                'adminId' => '',
            ]
            , 'GET');
        $res = AdminLogic::getAdminOne($this->verifyData['adminId']);

        return ['data'=>$res];
    }

    //获取登录后获取
    public function getAdminLogin(){

        $res = AdminLogic::getAdminLogin();

        return ['data'=>$res];
    }

    //修改密码
    public function editPassword(){
        $this->verify(
            [
                'passwordOld' => '',
                'passwordNew'=>'',
                'passwordCon'=>'',
            ]
            , 'POST');
        AdminLogic::editPassword($this->verifyData);
        return;
    }

    //重置密码
    public function resetPassword(){
        $this->verify(
            [
                'adminId' => '',
                'passwordNew' => '',
            ]
            , 'POST');
        AdminLogic::ResetPassword($this->verifyData);
        return;
    }

    //删除
    public function deleteAdmin(){
        $this->verify(
            [
                'AdminId' => '',//新闻ID
            ]
            , 'POST');
        AdminLogic::deleteAdmin($this->verifyData['AdminId']);

        return;
    }


}

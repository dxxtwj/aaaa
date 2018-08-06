<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\AdminLogic;
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
                'name' => '',//语言名称
                'password'=>''
            ]
            , 'POST');
        AdminLogic::addAdmin($this->verifyData);

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


}

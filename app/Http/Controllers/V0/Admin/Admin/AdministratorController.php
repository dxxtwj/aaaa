<?php

namespace App\Http\Controllers\V0\Admin\Admin;

use App\Logic\V0\Admin\AdministratorLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AdministratorController extends Controller
{
    //添加
    public function AddAdministrator()
    {
        $this->verify(
            [
                'administratorName' => '',//名称
                'administratorPassword'=>''
            ]
            , 'POST');
        AdministratorLogic::addAdministrator($this->verifyData);

    }

    //获取全部列表
    public function getAdministrator(){

        $res = AdministratorLogic::getAdministratorList();

        return $res;
    }

    //获取单条
    public function getAdministratorOne(){
        $this->verify(
            [
                'administratorId' => '',
            ]
            , 'GET');
        $res = AdministratorLogic::getAdministratorOne($this->verifyData['administratorId']);

        return ['data'=>$res];
    }

    //获取登录后获取
    public function getAdministratorLogin(){

        $res = AdministratorLogic::getAdministratorLogin();

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
        AdministratorLogic::editPassword($this->verifyData);
        return;
    }

    //重置密码
    public function resetPassword(){
        $this->verify(
            [
                'administratorId' => '',
                'administratorName' => '',
                'administratorPassword' => '',

            ]
            , 'POST');
        AdministratorLogic::ResetPassword($this->verifyData);
        return;
    }


    //删除
    public function deleteAdministrator(){
        $this->verify(
            [
                'administratorId' => '',//新闻ID
            ]
            , 'POST');
        AdministratorLogic::deleteAdministrator($this->verifyData['administratorId']);

        return;
    }


    //删除
    public function getSiteLists(){

       $res = AdministratorLogic::getSiteList();

        return ['lists'=>$res];
    }


}

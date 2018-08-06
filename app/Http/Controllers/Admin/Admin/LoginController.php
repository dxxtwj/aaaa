<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Logic\Admin\LoginLogic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class LoginController extends Controller
{
    public  function  index()
    {

    }
    /**
     * 登录
     */
    public function login()
    {
        $this->verify([
            'adminName'=>'',
            'adminPassword'=>''
        ],'POST');

        $data=LoginLogic::login($this->verifyData['adminName'],$this->verifyData['adminPassword']);
        return ['data'=>$data];
    }


    public function isLogin(){
        if (!LoginLogic::isLogin()){
            return ['data'=>[
                'isLogin'=>false
            ]];
        }
        else{
            $admin=LoginLogic::getAdmin();
        }
        return ['data'=>[
            'isLogin'=>true,
            'adminName'=>empty($admin->adminName) ? '' : $admin->adminName,
            'adminId'=>empty($admin->adminId) ? '' :$admin->adminId
        ]];
    }

    /*
     * 退出登陆
     */
    public function logout(){
        $data = LoginLogic::logout();
        return ['data'=>$data];
    }
}

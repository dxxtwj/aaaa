<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\LoginLogic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class LoginController extends Controller
{

    /**
     * 登录
     */
    public function login()
    {
        $this->verify([
            'name'=>'',
            'password'=>''
        ],'POST');

        $data=LoginLogic::login($this->verifyData['name'],$this->verifyData['password']);
        return ['data'=>$data];
    }


    public function isLogin(){
        if (!LoginLogic::isLogin()){
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }
        return ['data'=>[
            'isLogin'=>true
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

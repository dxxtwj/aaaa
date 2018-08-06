<?php

namespace App\Http\Controllers\V0\Api\User;

use App\Logic\V0\User\LoginLogic;
use App\Logic\V0\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //登录
    public function login()
    {
        $this->verify(
            [
                'phone' => '',
                'password' => '',
                'codeImg' => 'no_required',
            ]
            , 'POST');
        LoginLogic::login($this->verifyData);
        return;
    }

    //登录
    public function loginCount()
    {
        $this->verify(
            [
                'phone' => '',
            ]
            , 'POST');
        $count['loginCount'] = LoginLogic::LoginCount($this->verifyData['phone']);
        return ['data'=>$count];
    }

    //登录状态
    public function isLogin(){
        if (!LoginLogic::isLogin()){
            /* throw new RJsonError('没有登录', 'NO_LOGIN');*/
            return ['data'=>[
                'isLogin'=>false
            ]];
        }
        return ['data'=>[
            'isLogin'=>true
        ]];
    }

    //退出登录
    public function logout()
    {
        LoginLogic::logout();
        return;
    }

    //检测这个账号是否存在
    public function AccountDetect()
    {
        $this->verify(
            [
                'account' => '',
                'type' => '',
            ]
            , 'POST');
        UserLogic::checkAccount($this->verifyData);
        return;
    }

}

<?php

namespace App\Http\Controllers\Shopping\Api\WechatLogin;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\WechatLogin\WechatLoginLogic;

class WechatLoginController extends Controller
{


    /*
     * 获取CODE
     */
    public function login() {
        $this->verify(
            [
                'redirectUrl' => '',
                'returnUrl' => '',
            ]
            , 'GET');
        $res = WechatLoginLogic::login($this->verifyData);
        return $res;
    }

    /*
     * 获取openid
     */
    public function redirect() {

        $this->verify(
            [
                'code' => '',
            ]
            , 'GET');
        $res = WechatLoginLogic::redirect($this->verifyData['code']);
        return $res;
    }

    public function aa() {
        $res = WechatLoginLogic::aa();
        return $res;
    }

}

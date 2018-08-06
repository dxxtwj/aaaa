<?php

namespace App\Http\Controllers\Shangrui\Api\WechatLogin;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\WechatLogin\WechatLoginLogic;

class WechatLoginController extends Controller
{


    /*
     * 获取CODE
     */
    public function login() {
        $this->verify(
            [
                'jsCode' => '',
            ]
            , 'GET');
        $res = WechatLoginLogic::login($this->verifyData);
        return $res;
    }

}

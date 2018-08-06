<?php

namespace App\Http\Controllers\V0\Api\Join;

use App\Logic\V0\Join\JoinLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class JoinController extends Controller
{
    //加入我们
    public function AddJoin()
    {
        $this->verify(
            [
                'name' => '',//名称
                'phone' => '',//手机
                'numberQq' => 'no_required',//邮箱
                'wechat' => 'no_required',//微信号
                'type' => 'no_required',//
                'codeImg' => 'no_required',//图片验证码
            ]
            , 'POST');
        JoinLogic::addJoin($this->verifyData);

    }

}

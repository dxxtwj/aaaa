<?php

namespace App\Http\Controllers\V0\Api;

use App\Logic\V0\OurMessage\OurMessageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class OurMessageController extends Controller
{

    //添加站点
    public function AddOurMessage()
    {
        $this->verify(
            [
                'messageCateId' => 'no_required',//名称
                'ourMessagePerson' => '',//名称
                'ourMessagePhone' => '',//手机
                'ourMessageEmial' => '',//邮件
                'ourMessageContent' => '',//内容
            ]
            , 'POST');
        OurMessageLogic::addOurMessage($this->verifyData);

    }

}

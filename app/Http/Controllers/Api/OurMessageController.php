<?php

namespace App\Http\Controllers\Api;

use App\Logic\OurMessageLogic;
use App\Logic\MessageCateLogic;
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

    //获取列表
    public function getMessageCateLists()
    {
        $res = MessageCateLogic::getMessageCateLists();
        return ['lists'=>$res];
    }

}

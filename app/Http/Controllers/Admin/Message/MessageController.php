<?php

namespace App\Http\Controllers\Admin\Message;

use App\Logic\Message\MessageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MessageController extends Controller
{

    //获取列表
    public function getMessageLists()
    {
        $this->verify(
            [
                'messageCateId' => 'no_required',
                'messagePerson' => 'no_required',
                'languageId' => 'no_required',
                'isOn' => 'no_required',
            ]
            , 'GET');
        $res = MessageLogic::getMessageLists($this->verifyData);
        return $res;
    }

    //获取
    public function getMessageOne()
    {
        $this->verify(
            [
                'messageId' => '',
            ]
            , 'GET');
        $res = MessageLogic::getMessage($this->verifyData['messageId']);
        return ['data'=>$res];
    }

    //修改
    public function editMessage()
    {
        $this->verify(
            [
                'messageId' => '',//名称
                'isOn' => '',//手机
            ]
            , 'POST');
        MessageLogic::editMessage($this->verifyData,$this->verifyData['messageId']);

    }

    //删除
    public function deleteMessage(){
        $this->verify(
            [
                'messageId' => '',//新闻ID
            ]
            , 'POST');
        MessageLogic::delAffair($this->verifyData['messageId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'messageId' => '',
                'isOn' => ''
            ]
            , 'POST');
        MessageLogic::isShow($this->verifyData,$this->verifyData['messageId']);

        return;
    }


}

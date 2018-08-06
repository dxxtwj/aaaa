<?php

namespace App\Http\Controllers\V0\Admin\OurMessage;

use App\Logic\V0\OurMessage\MessageCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MessageCateController extends Controller
{

    //添加
    public function AddMessageCate()
    {
        $this->verify(
            [
                'messageCateName' => '',//名称
            ]
            , 'POST');
        MessageCateLogic::addMessageCate($this->verifyData);
        return;
    }

    //获取列表
    public function getMessageCateLists()
    {
        $res = MessageCateLogic::getMessageCateLists();
        return ['lists'=>$res];
    }

    //获取
    public function getMessageCateOne()
    {
        $this->verify(
            [
                'messageCateId' => '',
            ]
            , 'GET');
        $res = MessageCateLogic::getMessageCate($this->verifyData['messageCateId']);
        return ['data'=>$res];
    }

    //修改
    public function editMessageCate()
    {
        $this->verify(
            [
                'messageCateId' => '',//名称
                'messageCateName' => '',//手机
            ]
            , 'POST');
        MessageCateLogic::editMessageCate($this->verifyData,$this->verifyData['messageCateId']);
        return;
    }

    //删除
    public function deleteMessageCate(){
        $this->verify(
            [
                'messageCateId' => '',//新闻ID
            ]
            , 'POST');
        MessageCateLogic::deleteMessageCate($this->verifyData['messageCateId']);
        return;
    }


}

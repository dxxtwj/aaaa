<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\MessageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MessageController extends Controller
{
    //列表
    public function getLists()
    {
        $lists = MessageLogic::Lists();
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'messageId' => '',//语言名称
            ]
            , 'GET');
        $data = MessageLogic::getOne($this->verifyData['messageId']);
        if(!empty($data['point'])){
            $data['point']=json_decode($data['point'],true);
        }
        return ['data'=>$data];
    }

    //删除
    public function delete(){
        $this->verify(
            [
                'messageId' => '',
            ]
            , 'POST');
        MessageLogic::delete($this->verifyData['messageId']);
        return;
    }


}

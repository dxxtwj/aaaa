<?php

namespace App\Http\Controllers\V0\Admin\OurMessage;

use App\Logic\V0\OurMessage\OurMessageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class OurMessageController extends Controller
{

    //获取列表
    public function getOurMessageLists()
    {
        $this->verify(
            [
                'messageCateId' => 'no_required',
                'ourMessagePerson' => 'no_required',
                'siteId' => 'no_required',
                'isSee' => 'no_required'

            ]
            , 'GET');
        $res = OurMessageLogic::getOurMessageLists($this->verifyData);
        return $res;
    }

    //获取
    public function getOurMessageOne()
    {
        $this->verify(
            [
                'ourMessageId' => '',
            ]
            , 'GET');
        $res = OurMessageLogic::getOurMessage($this->verifyData['ourMessageId']);
        return ['data'=>$res];
    }

    //删除
    public function deleteOurMessage(){
        $this->verify(
            [
                'ourMessageId' => '',//新闻ID
            ]
            , 'POST');
        OurMessageLogic::deleteOurMessage($this->verifyData['ourMessageId']);

        return;
    }


}

<?php

namespace App\Http\Transfer;

use App\Logic\Transfer\TransferLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class Transfer extends Controller
{
    //获取全部列表
    public function getLists(){
        $this->verify(
            [
                'siteId' => '',
                'type' => '',
            ]
            , 'GET');
        $res = TransferLogic::get($this->verifyData);

        return ['lists'=>$res];
    }

    //获取全部列表
    public function getArtLists(){
        $this->verify(
            [
                'class' => '',
            ]
            , 'GET');
        $res = TransferLogic::getArt($this->verifyData['class']);

        return ['lists'=>$res];
    }

}

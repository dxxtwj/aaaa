<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\AboutLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AboutController extends Controller
{
    //列表
    public function getLists()
    {
        $data['isOn']=1;
        $lists = AboutLogic::Lists($data);
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'aboutId' => '',//语言名称
            ]
            , 'GET');
        $data = AboutLogic::getOne($this->verifyData['aboutId']);
        return ['data'=>$data];
    }


}

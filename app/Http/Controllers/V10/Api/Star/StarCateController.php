<?php

namespace App\Http\Controllers\V10\Api\Star;

use App\Logic\V10\Star\StarCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class StarCateController extends Controller
{
    //列表
    public function getLists()
    {
        $data['isOn']=1;
        $lists = StarCateLogic::Lists();
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'starCateId' => '',//语言名称
            ]
            , 'GET');
        $data = StarCateLogic::getOne($this->verifyData['starCateId']);
        return ['data'=>$data];
    }

    //单条
    public function getRecommend()
    {
        $this->verify(
            [
                'number' => '',//语言名称
            ]
            , 'GET');
        $data = StarCateLogic::getRecommend($this->verifyData['number']);
        return ['data'=>$data];
    }



}

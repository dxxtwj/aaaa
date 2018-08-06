<?php

namespace App\Http\Controllers\V10\Api\Star;

use App\Logic\V10\Star\StarLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class StarController extends Controller
{

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'starTitle' => 'no_required',//语言名称
                'starCateId' => 'no_required',//语言名称
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $lists = StarLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'starId' => '',//语言名称
            ]
            , 'GET');
        $data = StarLogic::getOneApi($this->verifyData['starId']);
        return ['data'=>$data];
    }

    //推荐
    public function getRecommend()
    {
        $this->verify(
            [
                'number' => '',//语言名称
            ]
            , 'GET');
        $res = StarLogic::recommend($this->verifyData['number']);
        return ['lists'=>$res];
    }

    //推荐
    public function getRecommendHome()
    {
        $this->verify(
            [
                'number' => '',//语言名称
            ]
            , 'GET');
        $res = StarLogic::recommendHomeApi($this->verifyData['number']);
        return ['lists'=>$res];
    }


}

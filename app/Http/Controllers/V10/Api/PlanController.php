<?php

namespace App\Http\Controllers\V10\Api;

use App\Logic\V10\PlanLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class PlanController extends Controller
{

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'planTitle' => 'no_required',//语言名称
            ]
            , 'GET');
        $this->verifyData['isOn']=1;
        $lists = PlanLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'planId' => '',//语言名称
            ]
            , 'GET');
        $data = PlanLogic::getOne($this->verifyData['planId']);
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
        $res = PlanLogic::recommend($this->verifyData['number']);
        return ['lists'=>$res];
    }

}

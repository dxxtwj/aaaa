<?php

namespace App\Http\Controllers\Admin\About;

use App\Logic\HonorLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class HonorController extends Controller
{
    //添加
    public function AddHonor()
    {
        $this->verify(
            [
                'isOn' =>'',
                'sort' =>'',
                'honorImage'=>'',
            ]
            , 'POST');
        HonorLogic::addHonor($this->verifyData);
    }

    //获取全部列表
    public function getHonorLists(){
        $res = HonorLogic::getHonorList($this->verifyData);
        return ['lists'=>$res];
    }

    //获取单条
    public function getHonorOne(){
        $this->verify(
            [
                'honorId' => '',
            ]
            , 'GET');
        $res = HonorLogic::getHonorOne($this->verifyData['honorId']);
        return ['data'=>$res];
    }

    //修改
    public function editHonor(){
        $this->verify(
            [
                'honorId' => '',//ID
                'isOn' =>'',
                'sort' =>'',
                'honorImage'=>'no_required',
            ]
            , 'POST');
        HonorLogic::editHonor($this->verifyData);

        return;
    }

    //删除
    public function deleteHonor(){
        $this->verify(
            [
                'honorId' => '',//新闻ID
            ]
            , 'POST');
        HonorLogic::deleteHonor($this->verifyData['honorId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'honorId' => '',
                'isOn' => ''
            ]
            , 'POST');
        HonorLogic::isShow($this->verifyData,$this->verifyData['honorId']);

        return;
    }



}

<?php

namespace App\Http\Controllers\V10\Admin\Star;

use App\Logic\V10\Star\StarLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class StarController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'starCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'recommendHome'=>'no_required',
                'starTitle'=>'no_required',
                'type'=>'no_required',
                'endorsementFee'=>'no_required',
                'appearanceFee'=>'no_required',
                'thumb'=>'no_required',
                'broker'=>'no_required',
                'phone'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        StarLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'starTitle' => 'no_required',//语言名称
                'starCateId' => 'no_required',//语言名称
            ]
            , 'GET');
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
        $data = StarLogic::getOne($this->verifyData['starId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'starId' => '',
                'starCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'recommendHome'=>'no_required',
                'starTitle'=>'no_required',
                'type'=>'no_required',
                'endorsementFee'=>'no_required',
                'appearanceFee'=>'no_required',
                'thumb'=>'no_required',
                'broker'=>'no_required',
                'phone'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        StarLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'starId' => '',
            ]
            , 'POST');
        StarLogic::delete($this->verifyData['starId']);
        return;
    }

    public function getRecommendHome()
    {
        $this->verify(
            [
                'starId' => '',
            ]
            , 'GET');
        $data = StarLogic::getRecommendHome($this->verifyData);
        return ['data'=>$data];
    }

    public function editRecommendHome()
    {
        $this->verify(
            [
                'starId' => '',
                'sort'=>'no_required',
                'isOn'=>'no_required',
                'starTitle'=>'no_required',
                'thumb'=>'no_required',
            ]
            , 'POST');
        StarLogic::editRecommendHome($this->verifyData);
        return;
    }

    public function recommendHomeLists()
    {
        $this->verify(
            [
                'starTitle'=>'no_required',
            ]
            , 'GET');
        $lists = StarLogic::recommendHomeLists($this->verifyData);
        return $lists;
    }

    public function recommendHomeDel()
    {
        $this->verify(
            [
                'starId'=>'',
            ]
            , 'POST');
        StarLogic::recommendHomeDel($this->verifyData['starId']);
        return;
    }

}

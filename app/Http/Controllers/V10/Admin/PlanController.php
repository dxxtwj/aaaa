<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\PlanLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class PlanController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'planTitle' => '',//语言名称
                'thumb'=>'no_required',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        PlanLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'planTitle' => 'no_required',//语言名称
            ]
            , 'GET');
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


    //修改
    public function edit(){
        $this->verify(
            [
                'planId' => '',//
                'planTitle' => '',//语言名称
                'thumb'=>'no_required',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDesc'=>'no_required'
            ]
            , 'POST');
        PlanLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'planId' => '',
            ]
            , 'POST');
        PlanLogic::delete($this->verifyData['planId']);
        return;
    }

}

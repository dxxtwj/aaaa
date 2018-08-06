<?php

namespace App\Http\Controllers\V10\Admin\Star;

use App\Logic\V10\Star\StarCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class StarCateController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'pid' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'starCateTitle'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        StarCateLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
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


    //修改密码
    public function edit(){
        $this->verify(
            [
                'starCateId' => '',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'starCateTitle'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        StarCateLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'starCateId' => '',
            ]
            , 'POST');
        StarCateLogic::delete($this->verifyData['starCateId']);
        return;
    }

    public function getCateIdTest(){
        $this->verify(
            [
                'starCateId' => '',
            ]
            , 'POST');
        $res = StarCateLogic::getCateId($this->verifyData['starCateId']);
        return ['data'=>$res];
    }

}

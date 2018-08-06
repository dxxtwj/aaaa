<?php

namespace App\Http\Controllers\V10\Admin\Cases;

use App\Logic\V10\Cases\CasesLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'casesCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'casesTitle'=>'no_required',
                'thumb'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        CasesLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'casesTitle' => 'no_required',//语言名称
                'casesCateId' => 'no_required',//语言名称
            ]
            , 'GET');
        $lists = CasesLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'casesId' => '',//语言名称
            ]
            , 'GET');
        $data = CasesLogic::getOne($this->verifyData['casesId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'casesId' => '',
                'casesCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'casesTitle'=>'no_required',
                'thumb'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        CasesLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'casesId' => '',
            ]
            , 'POST');
        CasesLogic::delete($this->verifyData['casesId']);
        return;
    }

    public function getContent()
    {
        $res = CasesLogic::getContent();
        return ['data'=>$res];
    }


}

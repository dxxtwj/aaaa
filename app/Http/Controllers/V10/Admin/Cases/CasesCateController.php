<?php

namespace App\Http\Controllers\V10\Admin\Cases;

use App\Logic\V10\Cases\CasesCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesCateController extends Controller
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
                'casesCateTitle'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        CasesCateLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $lists = CasesCateLogic::Lists();
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'casesCateId' => '',//语言名称
            ]
            , 'GET');
        $data = CasesCateLogic::getOne($this->verifyData['casesCateId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'casesCateId' => '',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'casesCateTitle'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        CasesCateLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'casesCateId' => '',
            ]
            , 'POST');
        CasesCateLogic::delete($this->verifyData['casesCateId']);
        return;
    }

}

<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\AboutLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AboutController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'aboutTitle'=>'',
                'aboutContent'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        AboutLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $lists = AboutLogic::Lists();
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


    //修改密码
    public function edit(){
        $this->verify(
            [
                'aboutId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'aboutTitle'=>'no_required',
                'aboutContent'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        AboutLogic::edit($this->verifyData);
        return;
    }


    public function delete(){
        $this->verify(
            [
                'aboutId' => '',
            ]
            , 'POST');
        AboutLogic::delete($this->verifyData['aboutId']);
        return;
    }


}

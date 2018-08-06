<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\LinkLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class LinkController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'image' => '',//语言名称
                'title' => 'no_required',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'url'=>'no_required'
            ]
            , 'POST');
        LinkLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $lists = LinkLogic::Lists();
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'linkId' => '',//语言名称
            ]
            , 'GET');
        $data = LinkLogic::getOne($this->verifyData['linkId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'linkId' => '',//语言名称
                'image' => '',//语言名称
                'title' => 'no_required',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'url'=>'no_required'
            ]
            , 'POST');
        LinkLogic::edit($this->verifyData);
        return;
    }


    public function delete(){
        $this->verify(
            [
                'linkId' => '',
            ]
            , 'POST');
        LinkLogic::delete($this->verifyData['linkId']);
        return;
    }


}

<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\PhotoLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'photoTitle' => '',//语言名称
                'thumb'=>'no_required',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        PhotoLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'photoTitle' => 'no_required',//语言名称
            ]
            , 'GET');
        $lists = PhotoLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'photoId' => '',//语言名称
            ]
            , 'GET');
        $data = PhotoLogic::getOne($this->verifyData['photoId']);
        return ['data'=>$data];
    }


    //修改
    public function edit(){
        $this->verify(
            [
                'photoId' => '',//语言名称
                'photoTitle' => '',//语言名称
                'thumb'=>'no_required',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDesc'=>'no_required'
            ]
            , 'POST');
        PhotoLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'photoId' => '',
            ]
            , 'POST');
        PhotoLogic::delete($this->verifyData['photoId']);
        return;
    }

}

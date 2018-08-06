<?php

namespace App\Http\Controllers\V10\Admin\News;

use App\Logic\V10\News\NewsLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'newsCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'newsTitle'=>'no_required',
                'thumb'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        NewsLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $this->verify(
            [
                'newsTitle' => 'no_required',//语言名称
                'newsCateId' => 'no_required',//语言名称
            ]
            , 'GET');
        $lists = NewsLogic::Lists($this->verifyData);
        return $lists;
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'newsId' => '',//语言名称
            ]
            , 'GET');
        $data = NewsLogic::getOne($this->verifyData['newsId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'newsId' => '',
                'newsCateId' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'newsTitle'=>'no_required',
                'thumb'=>'no_required',
                'content'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required'
            ]
            , 'POST');
        NewsLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'newsId' => '',
            ]
            , 'POST');
        NewsLogic::delete($this->verifyData['newsId']);
        return;
    }


}

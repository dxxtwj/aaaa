<?php

namespace App\Http\Controllers\Admin\Works;

use App\Logic\Works\WorksLogic;
use App\Logic\Works\WriterLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class WriterController extends Controller
{
    //添加
    public function AddWriter()
    {
        $this->verify(
            [
                'name' => '',//分类id
                'sex' => 'no_required',//分类id
                'sort' => 'no_required',//排序
                'headimg' => 'no_required',//排序
                'description' => 'no_required',//是否显示
            ]
            , 'POST');
        WriterLogic::addWriter($this->verifyData);

    }

    //获取全部列表
    public function getWriterLists(){
        $this->verify(
            [
                'name' => 'no_required',
                'sex'=>'no_required'
            ]
            , 'GET');
        $res = WriterLogic::getWriterList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getWriterOne(){
        $this->verify(
            [
                'worksId' => '',
            ]
            , 'GET');
        $res = WriterLogic::getWriter($this->verifyData['worksId']);

        return ['data'=>$res];
    }

    public function editWriter(){
        $this->verify(
            [
                'writerId'=>'',
                'name' => '',//分类id
                'sex' => 'no_required',//分类id
                'sort' => 'no_required',//排序
                'headimg' => 'no_required',//排序
                'description' => 'no_required',//是否显示
            ]
            , 'POST');
        WriterLogic::editWriter($this->verifyData);
        return;
    }
    //删除
    public function deleteWriter(){
        $this->verify(
            [
                'writerId' => '',//新闻ID
            ]
            , 'POST');
        WriterLogic::deleteWriter($this->verifyData['writerId']);
        return;
    }



}

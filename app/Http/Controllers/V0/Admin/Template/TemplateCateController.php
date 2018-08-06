<?php

namespace App\Http\Controllers\V0\Admin\Template;


use App\Logic\V0\Template\TemplateCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TemplateCateController extends Controller
{
    //添加
    public function AddTemplateCate()
    {
        $this->verify(
            [
                'pid' => '',
                'isOn' => '',//是否显示
                'sort'=>'no_required',
                'templateCateTitle'=>''//标题
            ]
            , 'POST');
        TemplateCateLogic::addTemplateCate($this->verifyData);
        return;
    }

    //获取全部列表
    public function getTemplateCateLists(){
        $this->verify(
            [
                'isOn' => 'no_required',//是否显示
                'templateCateTitle'=>'no_required'//标题
            ]
            , 'GET');
        $res = TemplateCateLogic::getTemplateCateList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getTemplateCateOne(){
        $this->verify(
            [
                'templateCateId' => '',//是否显示
            ]
            , 'GET');
        $res = TemplateCateLogic::getTemplateCateOne($this->verifyData);
        return ['data'=>$res];
    }

    //获取单条
    public function getParentsId(){
        $this->verify(
            [
                'templateCateId' => '',//是否显示
            ]
            , 'GET');
        $res = TemplateCateLogic::getParentsId($this->verifyData);
        return ['data'=>$res];
    }

    //修改
    public function editTemplateCate()
    {
        $this->verify(
            [
                'templateCateId' => '',
                'isOn' => '',//是否显示
                'sort'=>'no_required',
                'templateCateTitle'=>''//标题
            ]
            , 'POST');
        TemplateCateLogic::editTemplateCate($this->verifyData);

    }

    //删除
    public function deleteTemplateCate()
    {
        $this->verify(
            [
                'templateCateId' => '',
            ]
            , 'POST');
        TemplateCateLogic::deleteTemplateCate($this->verifyData);

    }



}

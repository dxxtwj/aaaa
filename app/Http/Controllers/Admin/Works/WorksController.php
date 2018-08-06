<?php

namespace App\Http\Controllers\Admin\Works;

use App\Logic\Works\WorksLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class WorksController extends Controller
{
    /*lang数组
        [
                languageId
                worksTitle、
                woksDesc、
                worksContent、
                siteTitle、
                siteKeywords、
                siteDescription、
         ]
     */
    //添加
    public function AddWorks()
    {
        $this->verify(
            [
                'worksCateId' => '',//分类id
                'writerId' => '',//分类id
                'sort' => '',//排序
                'worksNumber'=>'no_required',//编号
                'worksThumb' => 'no_required',//排序
                'isOn' => '',//是否显示
                'lang'=>'',//
            ]
            , 'POST');
        WorksLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getWorksLists(){
        $this->verify(
            [
                'worksCateId' => 'no_required',//分类id
                'worksTitle' => 'no_required',
                'languageId' => 'no_required',//语言ID
                'isOn'=>'no_required'
            ]
            , 'GET');
        $res = WorksLogic::getWorksList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getWorksOne(){
        $this->verify(
            [
                'worksId' => '',
            ]
            , 'GET');
        $res = WorksLogic::getWorksOne($this->verifyData['worksId']);

        return ['data'=>$res];
    }

    //修改
    /*lang数组
        [
                languageId
                worksTitle、
                woksDesc、
                worksContent、
                siteTitle、
                siteKeywords、
                siteDescription、
         ]
     */
    public function editWorks(){
        $this->verify(
            [
                'worksId'=>'',
                'worksCateId' => '',//分类id
                'writerId' => '',//分类id
                'sort' => '',//排序
                'worksNumber'=>'no_required',//编号
                'worksThumb' => 'no_required',//排序
                'isOn' => '',//是否显示
                'lang'=>'',//
            ]
            , 'POST');
        WorksLogic::editAll($this->verifyData);
        return;
    }
    //删除
    public function deleteWorks(){
        $this->verify(
            [
                'worksId' => '',//新闻ID
            ]
            , 'POST');
        WorksLogic::delAffair($this->verifyData['worksId']);

        return;
    }



}

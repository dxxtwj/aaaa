<?php

namespace App\Http\Controllers\Admin\Works;

use App\Logic\Works\WorksCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class WorksCateController extends Controller
{


    //添加
    public function AddWorksCate()
    {
        $this->verify(
            [
                'pid' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'worksThumb' => 'no_required',//图片
                'startTime' => '',//开始时间
                'endTime' => '',//结束时间
                'lang'=>'',//newsCateTitle、newsCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
            ]
            , 'POST');
        WorksCateLogic::addAll($this->verifyData);
    }

    //获取全部列表
    public function getWorksCateLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = WorksCateLogic::getWorksCateLists($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getWorksCateOne(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $res = WorksCateLogic::getWorksCateOne($this->verifyData['worksCateId']);

        return ['data'=>$res];
    }

    //修改
    public function editWorksCate()
    {
        $this->verify(
            [
                'worksCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'worksThumb' => 'no_required',//图片
                'startTime' => '',//开始时间
                'endTime' => '',//结束时间
                'lang'=>'',//newsCateTitle、newsCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
            ]
            , 'POST');
        WorksCateLogic::editAll($this->verifyData);
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $res = WorksCateLogic::getWorksCateId($this->verifyData['worksCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'worksCateId' => '',
            ]
            , 'GET');
        $res = WorksCateLogic::getChildId($this->verifyData['worksCateId']);
        return ['data'=>$res];
    }


    //删除
    public function deleteCasesCate(){
        $this->verify(
            [
                'worksCateId' => '',//分类id
            ]
            , 'POST');
        WorksCateLogic::delAffair($this->verifyData['worksCateId']);

        return;
    }



}

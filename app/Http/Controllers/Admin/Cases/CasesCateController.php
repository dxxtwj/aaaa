<?php

namespace App\Http\Controllers\Admin\Cases;

use App\Logic\Cases\CasesCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesCateController extends Controller
{
    /*
     *  lang[
     *      casesCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //添加
    public function AddCasesCate()
    {
        $this->verify(
            [
                'pid' => '',//分类id
                'isOn'=>'',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
                'tableId'=>'',
                'casesCateImage' => 'no_required',//图片
            ]
            , 'POST');
        CasesCateLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getCasesCateLists(){
        $this->verify(
            [
                'tableId'=>'',
                'languageId' => 'no_required',
                'isOn'=>'no_required'
            ]
            , 'GET');
        $res = CasesCateLogic::getCasesCateList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getCasesCateOne(){
        $this->verify(
            [
                'tableId'=>'no_required',
                'casesCateId' => '',
            ]
            , 'GET');
        $res = CasesCateLogic::getCasesCateOne($this->verifyData['casesCateId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      casesCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editCasesCate(){
        $this->verify(
            [
                'tableId'=>'',
                'casesCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
                'casesCateImage' => 'no_required',//图片
            ]
            , 'POST');
        CasesCateLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteCasesCate(){
        $this->verify(
            [
                'tableId'=>'no_required',
                'casesCateId' => '',//新闻ID
            ]
            , 'POST');
        CasesCateLogic::delAffair($this->verifyData['casesCateId']);

        return;
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'tableId'=>'no_required',
                'casesCateId' => '',
            ]
            , 'GET');
        $res = CasesCateLogic::getCasesCateId($this->verifyData['casesCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'tableId'=>'no_required',
                'casesCateId' => '',
            ]
            , 'GET');
        $res = CasesCateLogic::getChildId($this->verifyData['casesCateId']);
        return ['data'=>$res];
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'casesCateId' => '',
                'isOn' => ''
            ]
            , 'POST');
        CasesCateLogic::isShow($this->verifyData,$this->verifyData['casesCateId']);

        return;
    }


}

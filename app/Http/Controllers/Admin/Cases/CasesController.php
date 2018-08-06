<?php

namespace App\Http\Controllers\Admin\Cases;

use App\Logic\Cases\CasesLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class CasesController extends Controller
{
    //添加
    public function AddCases()
    {
        $this->verify(
            [
                'tableId'=>'',
                'casesCateId' => '',//分类id
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'recommend' => '',//推荐
                'casesImage' => 'no_required',//缩略图
                'lang'=>'',//语言数组--languageId、newsTitle、newsAuthor、newsContent、newsDesc、siteTitle、siteKeywords、siteDescription
                //'photos'=>'no_required',//图片数组--传CasesImagePic、CasesImageDesc、languageId三个字段
                'casesbanner'=>'no_required'

            ]
            , 'POST');
        CasesLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getCasesLists(){
        $this->verify(
            [
                'tableId'=>'',
                'casesCateId' => 'no_required',//分类id
                'casesTitle' => 'no_required',
                'languageId' => 'no_required',//语言ID
                'isOn'=>'no_required'
            ]
            , 'GET');
        $res = CasesLogic::getCasesList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getCasesOne(){
        $this->verify(
            [
                'casesId' => '',
            ]
            , 'GET');
        $res = CasesLogic::getCasesOne($this->verifyData['casesId']);

        return ['data'=>$res];
    }

    //修改
   /*lang数组
       [
               languageId
               newsTitle、
               newsAuthor、
               newsContent、
               newsDesc、
               siteTitle、
               siteKeywords、
               siteDescription、
               photos[
                    CasesImagePic、
                    CasesImageDesc、
                    languageId
                ]数组图片可不传
               contentFile[  文件
                    galleryUrl  图片
                ]
        ]
    */
    public function editCases(){
        $this->verify(
            [
                'tableId'=>'',
                'casesId' => '',//新闻ID
                'casesCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'recommend' => '',//推荐
                'casesImage' => '',//缩略图
                'lang'=>'',
                'casesbanner'=>'no_required',
            ]
            , 'POST');
        CasesLogic::editAll($this->verifyData);

        return;
    }
    //修改
    public function deleteCases(){
        $this->verify(
            [
                'casesId' => '',//新闻ID
            ]
            , 'POST');
        CasesLogic::delAffair($this->verifyData['casesId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'casesId' => '',
                'isOn' => ''
            ]
            , 'POST');
        CasesLogic::isShow($this->verifyData,$this->verifyData['casesId']);

        return;
    }



}

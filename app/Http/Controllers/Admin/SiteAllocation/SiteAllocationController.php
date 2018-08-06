<?php

namespace App\Http\Controllers\Admin\SiteAllocation;

use App\Logic\AboutLogic;
use App\Logic\SiteAllocation\SiteAllocationLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SiteAllocationController extends Controller
{
    //获取
    public function getAllocation()
    {
        $siteId = SiteId::getSiteId();
        //$siteId=7;
        $res = SiteAllocationLogic::getAllocation($siteId);
        return ['lists'=>$res];
    }

    //有多语言
    public function getAllocationLists()
    {
        /*$this->verify(
            [
                'siteId'=>'',//系统ID
                'languageId'=>'no_required',//系统ID
            ]
            , 'GET');*/
        $languageId=SiteId::getLanguageId();
        /*if(!empty($this->verifyData['languageId'])){
            $languageId=$this->verifyData['languageId'];
        }*/
        $siteId = SiteId::getSiteId();
        //$siteId=$this->verifyData['siteId'];
        $res = SiteAllocationLogic::getAllocationLists($siteId,$languageId);
        return ['lists'=>$res];
    }

    public function getSystemSeo()
    {
        $this->verify(
            [

                'languageId'=>'no_required',//
            ]
            , 'GET');
        $langId = SiteId::getLanguageId();
        $languageId = $this->verifyData['languageId'] ?? $langId;
        $index = '首页';
        $Page = '内页';
        if($languageId == 2){
            $index = 'index';
            $Page = 'page';
        }
        //首页
        $home=[
            'type'=>'index',
            'typeName'=>$index,
            'tableId'=>1,
        ];
        //内页
        $page=[
            'type'=>'page',
            'typeName'=>$Page,
            'tableId'=>1,
        ];
        $arr2[]=$home;
        $arr2[]=$page;
        $siteId = SiteId::getSiteId();
        $pro = SiteAllocationLogic::getAllocationProduct($siteId,$languageId);
        foreach ($pro as $key1=>$item) {
            $pro[$key1]['type']='product';
            $pro[$key1]['typeName']=$item['menuTypeName'];
        }
        $news = SiteAllocationLogic::getAllocationNews($siteId,$languageId);
        foreach ($news as $key2=>$item) {
            $news[$key2]['type']='news';
            $news[$key2]['typeName']=$item['menuTypeName'];
        }
        $cases = SiteAllocationLogic::getAllocationCases($siteId,$languageId);
        foreach ($cases as $key3=>$item) {
            $cases[$key3]['type']='case';
            $cases[$key3]['typeName']=$item['menuTypeName'];
        }
        $arr = array_merge($pro,$news,$cases);
        $res = array_merge($arr2,$arr);
        SiteAllocationLogic::defaultSeo($res);
        return ['lists'=>$res];
    }

    public function getSeoOne()
    {
        $this->verify(
            [
                'type' => '',//类型
                'tableId'=>'',//系统ID
                'languageId'=>'',//
            ]
            , 'GET');
        $res = SiteAllocationLogic::getSeoOne($this->verifyData);
        return ['data'=>$res];
    }

    public function editSeo()
    {
        $this->verify(
            [
                'type' => '',//类型
                'tableId'=>'',//系统ID
                'lang'=>'',
                /*'languageId'=>'',//
                'siteTitle'=>'no_required',
                'siteKeywords'=>'no_required',
                'siteDescription'=>'no_required',*/
            ]
            , 'POST');
        SiteAllocationLogic::edit($this->verifyData);
        return;
    }

    //获取内页列表
    public function getPageLists()
    {
        $this->verify(
            [
                'languageId'=>'',//
            ]
            , 'GET');
        $res = SiteAllocationLogic::getPageLists($this->verifyData['languageId']);
        return ['lists'=>$res];
    }

    public function getMenuTypeLists()
    {
        $this->verify(
            [
                'languageId'=>'',//
                'siteId'=>'',//
                'classType'=>'',//
            ]
            , 'GET');
        $languageId = $this->verifyData['languageId'];
        $classType = $this->verifyData['classType'];
        $siteId = $this->verifyData['siteId'];
        $res = SiteAllocationLogic::getMenuTypeLists($languageId,$siteId,$classType);
        if($classType==4){
            $arr['languageId']=$languageId;
            $res = AboutLogic::getAboutMenuList($arr);
        }
        return ['lists'=>$res];
    }

}

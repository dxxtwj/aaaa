<?php

namespace App\Http\Controllers\V0\Admin\Site;

use App\Logic\V0\Site\SiteLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SiteController extends Controller
{

    //添加站点
    public function AddSite()
    {
        $this->verify(
            [
                'siteName' => '',
                'siteDesc' => 'no_required',
                'siteStatus'=>'',
                'siteLogo'=>'no_required',
                'lang'=>'',//传一个数组，语言ID数组-languageId
            ]
            , 'POST');

        SiteLogic::addAffair($this->verifyData);

    }

    //修改状态
    public function Status()
    {
        $this->verify(
            [
                'siteId' => '',
                'siteStatus'=>'',
            ]
            , 'POST');
        SiteLogic::Status($this->verifyData);
        return;
    }

    //获取站点列表
    public function GetSiteLists()
    {
        $this->verify(
            [
                'siteName' => 'no_required',
            ]
            , 'GET');
        $this->verifyData['isDel']=1;
        $res = SiteLogic::getSiteLists($this->verifyData);
        return $res;
    }

    //获取站点回收站列表
    public function GetSiteBackLists()
    {
        $this->verify(
            [
                'siteName' => 'no_required'
            ]
            , 'GET');
        $this->verifyData['isDel']=0;
        $res = SiteLogic::getSiteLists($this->verifyData);
        return $res;
    }

    //获取站点
    /*public function getSiteOne()
    {
        $this->verify(
            [
                'siteId' => '',
            ]
            , 'GET');
        $res = SiteLogic::getSiteOne($this->verifyData['siteId']);
        return ['data'=>$res];
    }*/

    //获取站点单条->域名管理
    public function getSiteOne()
    {
        $this->verify(
            [
                'siteId' => '',
            ]
            , 'GET');
        $res = SiteLogic::getSiteOne($this->verifyData['siteId']);
        return ['data'=>$res];
    }

    //修改
    public function editSite(){
        $this->verify(
            [
                'siteId' => '',
                'siteName' => '',
                'siteDesc' => 'no_required',
                'siteLogo'=>'no_required',
                'siteStatus'=>'',
                'lang'=>''
            ]
            , 'POST');
        SiteLogic::editAffair($this->verifyData,$this->verifyData['siteId']);
        return;
    }

    //回收站
    public function delBack(){
        $this->verify(
            [
                'siteId' => '',//新闻ID
            ]
            , 'POST');
        SiteLogic::delBack($this->verifyData['siteId']);
    }

    //还原
    public function Reduction(){
        $this->verify(
            [
                'siteId' => '',//新闻ID
            ]
            , 'POST');
        SiteLogic::reduction($this->verifyData['siteId']);
    }

    //删除
    public function deleteSite(){
        $this->verify(
            [
                'siteId' => '',//新闻ID
            ]
            , 'POST');
        SiteLogic::deleteSite($this->verifyData['siteId']);

        return;
    }

    //获取站点语言列表
    public function getSiteLang(){
        $this->verify(
            [
                'siteId' => '',//新闻ID
            ]
            , 'GET');
        $lang = SiteLogic::getSiteLang($this->verifyData['siteId']);

        return ['lists'=>$lang];
    }



}

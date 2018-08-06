<?php

namespace App\Http\Controllers\V0\Admin\Site;

use App\Logic\V0\Site\DomainLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class DomainController extends Controller
{

    //添加站点
    public function AddDomain()
    {
        $this->verify(
            [
                'siteId' => '',
                'domainUrl' => '',
                'templateId'=>'no_required',
            ]
            , 'POST');

        DomainLogic::addAffair($this->verifyData);

    }

    //获取列表
    public function GetDomainLists()
    {
        $this->verify(
            [
                'siteId' => 'no_required',
                'domainUrl' => 'no_required'
            ]
            , 'GET');
        $res = DomainLogic::getDomainLists($this->verifyData);
        return $res;
    }

    //获取回收站列表
    public function GetDomainBackLists()
    {
        $this->verify(
            [
                'siteId' => 'no_required',
            ]
            , 'GET');
        $this->verifyData['isDel']=0;
        $res = DomainLogic::getDomainLists($this->verifyData);
        return $res;
    }

    //获取
    public function getDomainOne()
    {
        $this->verify(
            [
                'domainId' => '',
            ]
            , 'GET');
        $res = DomainLogic::getDomainOne($this->verifyData['domainId']);
        return ['data'=>$res];
    }

    //修改
    public function editDomain(){
        $this->verify(
            [
                'domainId' => '',
                'domainUrl' => '',
                'templateId'=>'no_required',
            ]
            , 'POST');
        DomainLogic::editAffair($this->verifyData,$this->verifyData['domainId']);
        return;
    }

    //回收站
    public function delBack(){
        $this->verify(
            [
                'domainId' => '',//新闻ID
            ]
            , 'POST');
        DomainLogic::delBack($this->verifyData['domainId']);
    }

    //还原
    public function Reduction(){
        $this->verify(
            [
                'domainId' => '',//新闻ID
            ]
            , 'POST');
        DomainLogic::reduction($this->verifyData['domainId']);
    }

    //删除
    public function deleteDomain(){
        $this->verify(
            [
                'domainId' => '',//新闻ID
            ]
            , 'POST');
        DomainLogic::deleteDomain($this->verifyData['domainId']);

        return;
    }




}

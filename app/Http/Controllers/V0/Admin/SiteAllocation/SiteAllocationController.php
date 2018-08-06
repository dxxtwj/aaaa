<?php

namespace App\Http\Controllers\V0\Admin\SiteAllocation;

use App\Logic\V0\SiteAllocation\SiteAllocationLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SiteAllocationController extends Controller
{
    /*
     * 添加网站-配置参数
     * */
    public function AddAllocation()
    {
        $this->verify(
            [
                'siteId'=>'',
                'allocationPro' => 'no_required',//产品-->里面有一个lang[]->languageId、productTypeName
                'allocationNews' => 'no_required',//新闻-->里面有一个lang[]->languageId、newsTypeName
                'allocationCases' => 'no_required',//案例-->里面有一个lang[]->languageId、casesTypeName
                'allocationLink' => 'no_required',//友情-->里面有一个lang[]->languageId、linkName
                'allocationMessage' => 'no_required',//留言-->里面有一个lang[]、languageId、messageName
            ]
            , 'POST');
        SiteAllocationLogic::add($this->verifyData);

    }

    /*
     * 产品配置添加
     * */
    public function addProduct()
    {
        $this->verify(
            [
                'siteId'=>'',
                'tableId'=>'',
                'lang'=>'',
            ]
            , 'POST');
        SiteAllocationLogic::increaseProduct($this->verifyData);
        return;
    }

    /*
     * 新闻配置添加
     * */
    public function addNews()
    {
        $this->verify(
            [
                'siteId'=>'',
                'tableId'=>'',
                'lang'=>'',
            ]
            , 'POST');
        SiteAllocationLogic::increaseNews($this->verifyData);
        return;
    }

    /*
     * 案例配置添加
     * */
    public function addCases()
    {
        $this->verify(
            [
                'siteId'=>'',
                'tableId'=>'',
                'lang'=>'',
            ]
            , 'POST');
        SiteAllocationLogic::increaseCases($this->verifyData);
        return;
    }

    /*
     * 友情链接配置添加
     * */
    public function addLink()
    {
        $this->verify(
            [
                'siteId'=>'',
                'lang'=>'',
            ]
            , 'POST');
        SiteAllocationLogic::increaseLink($this->verifyData);
        return;
    }

    /*
     * 留言配置添加
     * */
    public function addMessage()
    {
        $this->verify(
            [
                'siteId'=>'',
                'lang'=>'',
            ]
            , 'POST');
        SiteAllocationLogic::increaseMessage($this->verifyData);
        return;
    }

    /*
     * 获取
     * */
    public function getAllocation()
    {
        $this->verify(
            [
                'siteId'=>'',
                'languageId'=>''

            ]
            , 'GET');
        $res = SiteAllocationLogic::getAllocation($this->verifyData['siteId'],$this->verifyData['languageId']);
        return ['data'=>$res];
    }


    /*
     * 获取单条-pro
     * */
    public function getProOne()
    {
        $this->verify(
            [
                'allocationProId'=>'',
            ]
            , 'GET');
        $res = SiteAllocationLogic::getProOne($this->verifyData['allocationProId']);
        return ['data'=>$res];
    }
    /*
     * 获取单条-news
     * */
    public function getNewsOne()
    {
        $this->verify(
            [
                'allocationNewsId'=>'',
            ]
            , 'GET');
        $res = SiteAllocationLogic::getNewsOne($this->verifyData['allocationNewsId']);
        return ['data'=>$res];
    }

    /*
     * 获取单条-cases
     * */
    public function getCasesOne()
    {
        $this->verify(
            [
                'allocationCasesId'=>'',
            ]
            , 'GET');
        $res = SiteAllocationLogic::getCasesOne($this->verifyData['allocationCasesId']);
        return ['data'=>$res];
    }

    /*
     * 获取单条-Message
     * */
    public function getMessageOne()
    {
        $this->verify(
            [
                'allocationMessageId'=>'',
            ]
            , 'GET');
        $res = SiteAllocationLogic::getMessageOne($this->verifyData['allocationMessageId']);
        return ['data'=>$res];
    }

    /*
     * 获取单条-Link
     * */
    public function getLinkOne()
    {
        $this->verify(
            [
                'allocationLinkId'=>'',
            ]
            , 'GET');
        $res = SiteAllocationLogic::getLinkOne($this->verifyData['allocationLinkId']);
        return ['data'=>$res];
    }


    /*
     * 编辑产品
     * */
    public function editAllocationPro()
    {
        $this->verify(
            [
                'allocationProId'=>'',
                'tableId'=>'',
                'siteId'=>'',
                'lang'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::editProAffairs($this->verifyData);
        return;
    }
    /*
     * 编辑新闻
     * */
    public function editAllocationNews()
    {
        $this->verify(
            [
                'allocationNewsId'=>'',
                'languageId'=>'no_required',
                'siteId'=>'no_required',
                'tableId'=>'no_required',
                'lang'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::editNewsAffairs($this->verifyData);
        return;
    }
    /*
     * 编辑案例
     * */
    public function editAllocationCases()
    {
        $this->verify(
            [
                'allocationCasesId'=>'',
                'languageId'=>'no_required',
                'siteId'=>'no_required',
                'tableId'=>'no_required',
                'lang'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::editCasesAffairs($this->verifyData);
        return;
    }

    /*
     * 编辑留言
     * */
    public function editAllocationMessage()
    {
        $this->verify(
            [
                'allocationMessageId'=>'',
                'lang'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::editMessageAffairs($this->verifyData);
        return;
    }

    /*
     * 编辑友情链接
     * */
    public function editAllocationLink()
    {
        $this->verify(
            [
                'allocationLinkId'=>'',
                'lang'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::editLinkAffairs($this->verifyData);
        return;
    }

    /*
     * 产品配置删除
     * */
    public function delProduct()
    {
        $this->verify(
            [
                'allocationProId'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::deleteProduct($this->verifyData['allocationProId']);
        return;
    }
    /*
     * 新闻配置删除
     * */
    public function delNews()
    {
        $this->verify(
            [
               'allocationNewsId'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::deleteNews($this->verifyData['allocationNewsId']);
        return;
    }
    /*
     * 案例配置删除
     * */
    public function delCases()
    {
        $this->verify(
            [
                'allocationCasesId'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::deleteCases($this->verifyData['allocationCasesId']);
        return;
    }
    /*
     * 友情链接删除
     * */
    public function delLink()
    {
        $this->verify(
            [
                'allocationLinkId'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::deleteLink($this->verifyData['allocationLinkId']);
        return;
    }
    /*
     * 友情链接删除
     * */
    public function delMessage()
    {
        $this->verify(
            [
                'allocationMessageId'=>'',
            ]
            ,'POST');
        SiteAllocationLogic::deleteMessage($this->verifyData['allocationMessageId']);
        return;
    }

    /*
     * 测试分类的一个方法
     */
    public function ceShiList() {
        $this->verify(
            [
                'siteId'=>'',//站点id
                'languageId' => '',//需要的语言

            ]
            ,'GET');
        $res = SiteAllocationLogic::ceShiList($this->verifyData);
        return $res;
    }

    /*
     * 测试分类的一个方法2
     */
    public function ceShiList2() {

        $this->verify(
            [
                'siteId'=>'',//站点id
                'languageId' => '',//需要的语言

            ]
            ,'GET');
        $res = SiteAllocationLogic::ceShiList2($this->verifyData);
        return $res;
    }


}

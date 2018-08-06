<?php

namespace App\Http\Controllers\Admin\News;

use App\Logic\News\NewsCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsCateController extends Controller
{
    //添加
    public function AddNewsCate()
    {
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'pid' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'lang'=>'',//newsCateTitle、newsCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
                'newsCateImage' => 'no_required',//图片
                /*'newsCateTitle' => '',//分类名称
                'siteTitle' => 'no_required',//seo站点标题
                'siteKeywords' => 'no_required',//站点关键字
                'siteDescription' => 'no_required',//描述*/
            ]
            , 'POST');
        NewsCateLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getNewsCateLists(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = NewsCateLogic::getNewsCateList($this->verifyData,$this->verifyData['tableId']);

        return ['lists'=>$res];
    }

    //获取全部列表----test
    public function getCateLists(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
            ]
            , 'GET');
        $res = NewsCateLogic::getCateList($this->verifyData['tableId']);

        return ['lists'=>$res];
    }

    //获取单条
    public function getNewsCateOne(){
        $this->verify(
            [
                'newsCateId' => '',
            ]
            , 'GET');
        $res = NewsCateLogic::getNewsCateOne($this->verifyData['newsCateId']);

        return ['data'=>$res];
    }

    //修改
    public function editNewsCate(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'newsCateImage' => 'no_required',//图片
                'lang'=>'',//newsCateTitle、newsCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
            ]
            , 'POST');
        NewsCateLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteNewsCate(){
        $this->verify(
            [
                'newsCateId' => '',//新闻ID
            ]
            , 'POST');
        NewsCateLogic::delAffair($this->verifyData['newsCateId']);

        return;
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'newsCateId' => '',
            ]
            , 'GET');
        $res = NewsCateLogic::getNewsCateId($this->verifyData['newsCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'newsCateId' => '',
            ]
            , 'GET');
        $res = NewsCateLogic::getChildId($this->verifyData['newsCateId']);
        return ['data'=>$res];
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'newsCateId'=>'',
                'isOn' => ''
            ]
            , 'POST');
        $data['isOn']=$this->verifyData['isOn'];
        NewsCateLogic::isShow($data,$this->verifyData['newsCateId']);

        return;
    }

    //测试
    /*public function getParents(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => '',
            ]
            , 'GET');
        $res = NewsCateLogic::getNewsCateIdTest($this->verifyData['newsCateId'],$this->verifyData['tableId']);
        return ['data'=>$res];
    }*/

}

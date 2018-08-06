<?php

namespace App\Http\Controllers\Admin;

use App\Logic\BannerLogic;
use App\Logic\BannersLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BannerController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      bannerTitle     标题
     *  ]
     */
    //添加
    public function AddBanner()
    {
        $this->verify(
            [
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'isOpen'=>'',//是否打开新窗口
                'isUrl'=>'no_required',//是否连接跳转
                'bannerUrlType'=>'no_required',//打开窗口类型
                'menuUrl'=>'no_required',//链接地址
                'menuId'=>'',//菜单ID
                'lang'=>'no_required',//语言数组--bannerTitle、bannerDesc
                'bannerUrl' => 'no_required',//链接
                'bannerImage' => 'no_required',//图片
                'systemType'=>'no_required',
                'labelId'=>'no_required',
            ]
            , 'POST');
        BannerLogic::addAll($this->verifyData);

    }


    //获取全部列表
    public function getBannerLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = BannerLogic::getBannerList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取全部列表
    public function getBannerListsByMenu(){
        $this->verify(
            [
                'languageId' => '',
                'isOn'=>'no_required',
            ]
            , 'GET');
        $res = BannerLogic::getBannerListsByMenuId($this->verifyData);

        return ['lists'=>$res];
    }

    //根据菜单ID删除广告
    public function deleteBannerByMenuId()
    {
        $this->verify(
            [
                'menuId' => '',//新闻ID
            ]
            , 'POST');
        BannerLogic::deleteBannerByMenuId($this->verifyData['menuId']);

        return;
    }


    //获取单条
    public function getBannerOne(){
        $this->verify(
            [
                'bannerId' => '',
            ]
            , 'GET');
        $res = BannerLogic::getBannerOne($this->verifyData['bannerId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      bannerTitle     标题
     *  ]
     */
    //修改
    public function editBanner(){
        $this->verify(
            [
                'bannerId' => '',//新闻ID
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'isOpen'=>'',//是否打开新窗口
                'isUrl'=>'no_required',//是否连接跳转
                'bannerUrlType'=>'no_required',//打开窗口类型
                'menuUrl'=>'no_required',//链接地址
                'menuId'=>'',//菜单ID
                'lang'=>'',//语言数组--bannerTitle
                'bannerUrl' => 'no_required',//链接
                'bannerImage' => 'no_required',//图片
                'systemType'=>'no_required',//系统类型
                'labelId'=>'no_required',//套数

            ]
            , 'POST');
        BannerLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteBanner(){
        $this->verify(
            [
                'bannerId' => '',//新闻ID
            ]
            , 'POST');
        BannerLogic::delAffair($this->verifyData['bannerId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'bannerId' => '',
                'isOn' => ''
            ]
            , 'POST');
        BannerLogic::isShow($this->verifyData,$this->verifyData['bannerId']);

        return;
    }



}

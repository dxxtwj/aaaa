<?php

namespace App\Http\Controllers\Admin\Product;

use App\Logic\Product\ProductCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class ProductCateController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      productCateTitle    产品名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *
     *  ]
     */
    //添加
    public function AddProductCate()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'pid' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'recommend'=>'no_required',//推荐
                'lang'=>'',//ProductCateTitle、ProductCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
                'productCateImage' => 'no_required',//图片
                'recommendImage' => 'no_required',//推荐图片
            ]
            , 'POST');
        ProductCateLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getProductCateLists(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'languageId' => 'no_required',
                'isOn' => 'no_required',
            ]
            , 'GET');
        $res = ProductCateLogic::getProductCateList($this->verifyData,$this->verifyData['tableId']);

        return ['lists'=>$res];
    }

    //获取单条
    public function getProductCateOne(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',
            ]
            , 'GET');
        $res = ProductCateLogic::getProductCateOne($this->verifyData['productCateId'],$this->verifyData['tableId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      productCateTitle    产品名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editProductCate(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'recommend'=>'no_required',//推荐
                'productCateImage' => 'no_required',//图片
                'recommendImage' => 'no_required',//推荐图片
                'lang'=>'',//ProductCateTitle、ProductCateImage、siteTitle、siteKeywords、siteKeywords、siteDescription
            ]
            , 'POST');
        ProductCateLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteProductCate(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//新闻ID
            ]
            , 'POST');
        ProductCateLogic::delAffair($this->verifyData['productCateId'],$this->verifyData['tableId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'productCateId'=>'',
                'isOn' => ''
            ]
            , 'POST');
        $data['isOn']=$this->verifyData['isOn'];
        ProductCateLogic::isShow($data,$this->verifyData['productCateId']);

        return;
    }

    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',
            ]
            , 'GET');
        $res = ProductCateLogic::getProductCateId($this->verifyData['productCateId'],$this->verifyData['tableId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',
            ]
            , 'GET');
        $res = ProductCateLogic::getChildId($this->verifyData['productCateId'],$this->verifyData['tableId']);
        return ['data'=>$res];
    }


}

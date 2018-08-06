<?php

namespace App\Http\Controllers\Admin\Product;

use App\Logic\Product\ProductLogic;
use App\Logic\Product\ProductCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      productTitle    产品名称
     *      productDesc     描述
     *      productContent  内容
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *      attribute[
     *          languageId          语言ID
     *          attributeName      名称
     *          attributeContent   内容
     *      ]
     *      photos[
     *          languageId          语言ID
     *          productImagePic     图片
     *          productImageDesc    图片描述或者标题
     *      ]
     *      contentFile[    文件
     *          galleryUlr          图片
     *      ]
     *  ]
     *  banner[
     *      languageId          语言ID
     *      productBannerPic     图片
     *      productBannerDesc    图片描述或者标题
     *  ]
     *  teacher[
     *      teacherId   教师ID
     *      classId     产品ID
     *  ]
     */
    //添加
    public function AddProduct()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//分类id
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'recommend' => '',//推荐
                'productNumber'=>'no_required',//产品编号
                'productThumb' => 'no_required',//缩略图
                'productOldPrice' => 'no_required',//原价
                'productSalePrice' => 'no_required',//现价
                'probanner'=>'no_required',
                'teacher'=>'no_required',//教师数组
                'lang'=>'',
            ]
            , 'POST');
        ProductLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getProductLists(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => 'no_required',//分类id
                'productTitle' => 'no_required',
                'languageId'=>  'no_required'
            ]
            , 'GET');
        $res = ProductLogic::getProductList($this->verifyData,$this->verifyData['tableId']);
        return $res;
    }

    //获取单条
    public function getProductOne(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productId' => '',
            ]
            , 'GET');
        $res = ProductLogic::getProductOne($this->verifyData['productId'],$this->verifyData['tableId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      productTitle    产品名称
     *      productDesc     描述
     *      productContent  内容
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *      attribute[
     *          languageId          语言ID
     *          attribute_name      名称
     *          attribute_content   内容
     *      ]
     *      photos[
     *          languageId          语言ID
     *          productImagePic     图片
     *          productImageDesc    图片描述或者标题
     *      ]
     *      banner[
     *          languageId          语言ID
     *          productBannerPic     图片
     *          productBannerDesc    图片描述或者标题
     *      ]
     *  ]
     */
    //修改
    public function editProduct(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productId' => '',//新闻ID
                'productCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'recommend' => '',//推荐
                'productNumber'=>'no_required',//产品编号
                'productThumb' => 'no_required',//缩略图
                'productOldPrice' => 'no_required',//原价
                'productSalePrice' => 'no_required',//现价
                'probanner'=>'no_required',
                'teacher'=>'no_required',//教师数组
                'lang'=>'',
            ]
            , 'POST');
        ProductLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteProduct(){
        $this->verify(
            [
                'productId' => '',//新闻ID
            ]
            , 'POST');
        ProductLogic::delAffair($this->verifyData['productId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'productId'=>'',
                'isOn' => ''
            ]
            , 'POST');
        $data['isOn']=$this->verifyData['isOn'];
        ProductLogic::isShow($data,$this->verifyData['productId']);

        return;
    }

    //测试获取ID
    public function getCateId(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//新闻ID
            ]
            , 'POST');
        $res = ProductCateLogic::getCateId($this->verifyData['productCateId'],$this->verifyData['tableId']);

        return ['data'=>$res];
    }

    //测试多套产品列表
    public function getProductTypeLists(){
        $res1['tableId']=1;
        $res2['tableId']=2;
        $res3['tableId']=3;
        $res4['tableId']=4;
        $res5['tableId']=5;
        $arr[]=$res1['tableId'];
        $arr[]=$res2['tableId'];
        $arr[]=$res3['tableId'];
        $arr[]=$res4['tableId'];
        $arr[]=$res5['tableId'];
        return ['lists'=>$arr];
    }

}

<?php

namespace App\Http\Controllers\Api\Product;

use App\Logic\Product\ProductCateLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class ProductCateController extends Controller
{

    //获取全部列表
    public function getProductCateLists(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
            ]
            , 'GET');
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = ProductCateLogic::getProductCateList($data,$this->verifyData['tableId']);

        return ['lists'=>$res];
    }



    //获取单条
    public function getProductCateOne(){
        $this->verify(
            [
                //'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductCateLogic::getProductCate($this->verifyData['productCateId'],$languageId);

        return ['data'=>$res];
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

    //获取分类名称--热搜用
    public function getClassName(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'proCateNumber' => '',//查多少个
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductCateLogic::getCalssName($this->verifyData['tableId'],$this->verifyData['proCateNumber'],$languageId);
        return ['lists'=>$res];
    }

    //获取下一级所有
    public function getProductKids()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//新闻ID
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductCateLogic::getProductCateKids($this->verifyData['productCateId'],$this->verifyData['tableId'],$languageId,$_GET['recommend'] ?? 0);
        return ['lists'=>$res];
    }

    //获取顶级
    public function getParents(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductCateLogic::getParents($this->verifyData['tableId'],$languageId);
        return ['lists'=>$res];
    }


}

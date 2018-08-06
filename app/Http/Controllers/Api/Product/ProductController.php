<?php

namespace App\Http\Controllers\Api\Product;

use App\Logic\Product\ProductLogic;
use App\Logic\Product\ProductCateLogic;
use App\Http\Middleware\SiteId;
use App\Logic\Menu\MenuLogic;
use App\Model\Product\ProductModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class ProductController extends Controller
{

    //获取全部列表
    public function getProductLists(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => 'no_required',//分类id
                'productTitle' => 'no_required',
                'productCateTitle' => 'no_required',
                'sort'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $this->verifyData['isOn']=1;
        $res = ProductLogic::getProductList($this->verifyData,$this->verifyData['tableId']);
        return $res;
    }

    //获取单条
    public function getProductOne(){
        $this->verify(
            [
                'productId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductLogic::getProduct($this->verifyData['productId'],$languageId);
        if(isset($res)){
            $last=ProductLogic::getLast($res['productCateId'],$languageId,$res['sort']);
            $next=ProductLogic::getNext($res['productCateId'],$languageId,$res['sort']);

            $res['lastId']=empty($last['productId']) ? '' : $last['productId'];
            $res['lastTitle'] =empty($last['productTitle']) ? '' : $last['productTitle'];
            $res['lastImg'] =empty($last['productThumb']) ? '' : $last['productThumb'];
            $res['lastDesc'] =empty($last['productDesc']) ? '' : $last['productDesc'];
            $res['lastCreatedAt'] =empty($last['createdAt']) ? '' : $last['createdAt'];


            $res['nextId']=empty($next['productId']) ? '' : $next['productId'];
            $res['nextTitle'] =empty($next['productTitle']) ? '' : $next['productTitle'];
            $res['nextImg'] =empty($next['productThumb']) ? '' : $next['productThumb'];
            $res['nextDesc'] =empty($next['productDesc']) ? '' : $next['productDesc'];
            $res['nextCreatedAt'] =empty($next['createdAt']) ? '' : $next['createdAt'];

        }
        return ['data'=>$res];
    }

    //产品模糊搜索
    public function getProductSearch() {

        $this->verify(
            [
                'productTitle' => 'no_required',//有属性的推荐
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductLogic::getProductSearch($languageId,$this->verifyData);
        return $res;
    }

    //获取推荐
    public function getRecommend(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'number' => '',//查多少条
                'productCateId' => 'no_required',//查多少条
                'attribute' => 'no_required',//有属性的推荐
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = ProductLogic::getRecommend($languageId,$this->verifyData['number'],$this->verifyData['tableId'],$this->verifyData['productCateId'] ?? 0,$this->verifyData['attribute'] ?? 0);
        return ['lists'=>$res];
    }

    //测试获取ID
    public function getCateId(){
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'productCateId' => '',//新闻ID
            ]
            , 'POST');
        $res = ProductCateLogic::test($this->verifyData['productCateId']);
        return ['data'=>$res];
    }

    //测试多套产品列表
    public function getProductTypeLists(){
        $siteId = SiteId::getSiteId();
        $arr=[];
        if($siteId==5){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统1';
            $res2['tableId']=2;
            $res2['productTypeName']='产品系统2';
            $res3['tableId']=3;
            $res3['productTypeName']='产品系统3';
            $res4['tableId']=4;
            $res4['productTypeName']='产品系统4';
            $res5['tableId']=5;
            $res5['productTypeName']='产品系统5';
            $arr[]=$res1;
            $arr[]=$res2;
            $arr[]=$res3;
            $arr[]=$res4;
            $arr[]=$res5;
        }
        if($siteId==11){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $res2['tableId']=2;
            $res2['productTypeName']='厂房设备';
            $arr[]=$res1;
            $arr[]=$res2;
        }
        if($siteId==13){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $arr[]=$res1;
        }
        return ['data'=>$arr];
    }

    //获取导航列表
    public function getProductMenuName()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'productId' => 'no_required',
                'productCateId'=>'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res=[];
        if(empty($this->verifyData['productId']) && empty($this->verifyData['productCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'PRODUCT_ERROR');
        }
        if(!empty($this->verifyData['productId'])){
            $res = ProductLogic::getMenuName($this->verifyData['productId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['productCateId'])){
            $res = ProductCateLogic::getProductCateParents($this->verifyData['productCateId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $menu = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            $res = MenuLogic::getMenuParents($menu['menuId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $res = MenuLogic::getMenuParents($this->verifyData['menuId'],$languageId);
            if(!empty($res)){
                return ['lists'=>$res];
            }
        }
        return ['lists'=>$res];
    }

    //获取分类下的所有产品
    public function getProductByCateId()
    {
        $this->verify(
            [
                'tableId' => '',
                'proNumber' => '',
                'cateNumber' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        //获取分类列表
        $cateLists = ProductCateLogic::getProductCateLists($this->verifyData['tableId'],$languageId,$this->verifyData['cateNumber']);
        foreach ($cateLists as $key=>$value) {
            $res = ProductLogic::getProductByCateId($value['productCateId'],$this->verifyData['proNumber'],$languageId);
            $cateLists[$key]['productLists']=$res;
        }
        return ['lists'=>$cateLists];
    }

}

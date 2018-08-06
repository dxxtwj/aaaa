<?php

namespace App\Http\Controllers\Api;

use App\Logic\BannerLogic;
use App\Http\Middleware\SiteId;
use App\Logic\BannersLogic;
use App\Logic\Menu\MenuLogic;
use App\Logic\Product\ProductCateLogic;
use App\Logic\Product\ProductLogic;
use App\Logic\News\NewsCateLogic;
use App\Logic\News\NewsLogic;
use App\Logic\Cases\CasesCateLogic;
use App\Logic\Cases\CasesLogic;
use App\Logic\About\AboutLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BannerController extends Controller
{

    //获取全部列表
    public function getBannerLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = BannerLogic::getBannerList($data);

        return ['lists'=>$res];
    }

    //获取单条
    public function getBannerOne(){
        $this->verify(
            [
                'bannerId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = BannerLogic::getBanner($this->verifyData['bannerId'],$languageId);

        return ['data'=>$res];
    }

    //获取单条
    public function getBannerMenu(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = BannerLogic::getBannerByMenuId($this->verifyData['menuId'],$languageId);

        return ['lists'=>$res];
    }

    //获取首页广告图->systemType
    public function getBannerByUrl(){
        $this->verify(
            [
                'systemType' => '',
                'labelId'=>'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = BannerLogic::getBannerByUrl($this->verifyData,$languageId);

        return ['lists'=>$res];
    }

    //产品-->拿广告
    public function getProductMenuBanner()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'productId' => 'no_required',
                'productCateId' => 'no_required',
                'menuId' => 'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if (empty($this->verifyData['productId']) && empty($this->verifyData['productCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])) {
            throw new RJsonError('请输入信息', 'PRODUCT_ERROR');
        }
        if (!empty($this->verifyData['menuId'])) {
            $banner = BannerLogic::getBannerByMenu($this->verifyData['menuId'], $languageId);
            if ($banner) {
                return ['lists' => $banner];
            }
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $res = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if($res){
                $banner = BannerLogic::getBannerByMenu($res['menuId'], $languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
        }
        if (!empty($this->verifyData['productCateId'])) {
            $res = ProductCateLogic::getProductCateParent($this->verifyData['productCateId'], $languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $pro=ProductCateLogic::getProductCate($this->verifyData['productCateId'],$languageId);
                $data['systemType']='product';
                $data['labelId']=$pro['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }

        }
        if(!empty($this->verifyData['productId'])){
            $res2 = ProductLogic::getMenuBanner($this->verifyData['productId'],$languageId);
            $res = ProductCateLogic::getProductCateParent($res2['productCateId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $pro=ProductCateLogic::getProductCate($res2['productCateId'],$languageId);
                $data['systemType']='product';
                $data['labelId']=$pro['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }
        }
        $index['systemType']='index';
        $banner = BannerLogic::getBannerByUrl($index,$languageId);
        return ['lists' => $banner];

    }

    //新闻-->拿广告
    public function getNewsMenuBanner()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'newsId' => 'no_required',
                'newsCateId'=>'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if(empty($this->verifyData['newsId']) && empty($this->verifyData['newsCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'NEWS_ERROR');
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $res = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if($res){
                $banner = BannerLogic::getBannerByMenu($res['menuId'], $languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $banner = BannerLogic::getBannerByMenu($this->verifyData['menuId'],$languageId);
            if($banner){
                return ['lists'=>$banner];
            }
        }
        if(!empty($this->verifyData['newsCateId'])){
            $res = NewsCateLogic::getNewsCateParent($this->verifyData['newsCateId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $news=NewsCateLogic::getNewsCate($this->verifyData['newsCateId'],$languageId);
                $data['systemType']='news';
                $data['labelId']=$news['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }
        }
        if(!empty($this->verifyData['newsId'])){
            $res2 = NewsLogic::getMenuBanner($this->verifyData['newsId'],$languageId);
            $res = NewsCateLogic::getNewsCateParent($res2['newsCateId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $news=NewsCateLogic::getNewsCate($res2['newsCateId'],$languageId);
                $data['systemType']='news';
                $data['labelId']=$news['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }
        }
        $index['systemType']='index';
        $banner = BannerLogic::getBannerByUrl($index,$languageId);
        return ['lists' => $banner];
    }

    //案例-->拿广告
    public function getCasesMenuBanner()
    {
        $this->verify(
            [
                'urlName' => 'no_required',//没有点击
                'tableId' => 'no_required',//没有点击
                'casesId' => 'no_required',
                'casesCateId'=>'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if(empty($this->verifyData['casesId']) && empty($this->verifyData['casesCateId']) && empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'CASES_ERROR');
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            $res = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if($res){
                $banner = BannerLogic::getBannerByMenu($res['menuId'], $languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $banner = BannerLogic::getBannerByMenu($this->verifyData['menuId'],$languageId);
            if($banner){
                return ['lists'=>$banner];
            }
        }
        if(!empty($this->verifyData['casesCateId'])){
            $res = CasesCateLogic::getCasesCateParent($this->verifyData['casesCateId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $cases=CasesCateLogic::getCasesCate($this->verifyData['casesCateId'],$languageId);
                $data['systemType']='case';
                $data['labelId']=$cases['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }
        }
        if(!empty($this->verifyData['casesId'])){
            $res2 = CasesLogic::getMenuBanner($this->verifyData['casesId'],$languageId);
            $res = CasesCateLogic::getCasesCateParent($res2['casesCateId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
            if(empty($res)){
                //获取单条
                $cases=CasesCateLogic::getCasesCate($res2['casesCateId'],$languageId);
                $data['systemType']='case';
                $data['labelId']=$cases['tableId'];
                $res = BannerLogic::getBannerByUrl($data,$languageId);
                if($res){
                    return['lists'=>$res];
                }
            }
        }
        $index['systemType']='index';
        $banner = BannerLogic::getBannerByUrl($index,$languageId);
        return ['lists' => $banner];
    }

    //作品
    public function getWorksMenuBanner()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if(empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'PRODUCT_ERROR');
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            //$url='http://127.0.0.1:1000/lists/works/1';
            //$url='/web/lists/works/1?xxxId=16';
            $res = MenuLogic::getWorksMenuBanner($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $banner = BannerLogic::getBannerByMenu($this->verifyData['menuId'],$languageId);
            if($banner){
                return ['lists'=>$banner];
            }
        }
        $index['systemType']='index';
        $banner = BannerLogic::getBannerByUrl($index,$languageId);
        return ['lists' => $banner];
    }

    //师资团队
    public function getTeacherMenuBanner()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
                'menuId'=>'no_required'
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        if(empty($this->verifyData['menuId']) && empty($this->verifyData['urlName']) && empty($this->verifyData['tableId'])){
            throw new RJsonError('请输入信息', 'PRODUCT_ERROR');
        }
        if(!empty($this->verifyData['urlName']) && !empty($this->verifyData['tableId'])){
            //拿到menuId
            //$url='http://127.0.0.1:1011/lists/teacher/1';
            //$url='/web/lists/works/1?xxxId=16';
            $res = MenuLogic::getTeacherMenuBanner($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
            if($res){
                $banner = BannerLogic::getMenuArr($res,$languageId);
                if ($banner) {
                    return ['lists' => $banner];
                }
            }
        }
        if(!empty($this->verifyData['menuId'])){
            $banner = BannerLogic::getBannerByMenu($this->verifyData['menuId'],$languageId);
            if($banner){
                return ['lists'=>$banner];
            }
        }
        $index['systemType']='index';
        $banner = BannerLogic::getBannerByUrl($index,$languageId);
        return ['lists' => $banner];
    }

}

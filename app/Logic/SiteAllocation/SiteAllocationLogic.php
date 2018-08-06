<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\SiteAllocation;

use App\Logic\LanguageLogic;
use App\Model\About\AboutModel;
use App\Model\SiteAllocation\AllocationProductModel;
use App\Model\SiteAllocation\AllocationNewsModel;
use App\Model\SiteAllocation\AllocationCasesModel;
use App\Model\SiteAllocation\AllocationLinkModel;
use App\Model\SiteAllocation\AllocationMessageModel;
use App\Model\SiteAllocation\SiteSeoModel;
use App\Model\V0\SiteAllocation\AllocationCasesLangModel;
use App\Model\V0\SiteAllocation\AllocationNewsLangModel;
use App\Model\V0\SiteAllocation\AllocationProLangModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class SiteAllocationLogic
{
    public static function getAllocation($siteId){
        $arr=[];
        $pro = self::getProduct($siteId);

        $news = self::getNews($siteId);

        $cases = self::getCases($siteId);

        $link = self::getLink($siteId);
        $message = self::getMessage($siteId);
        $teacher = self::getTeacher($siteId);
        $arr['product']=empty($pro) ? [] : $pro;
        $arr['news']=empty($news) ? [] : $news;
        $arr['cases']=empty($cases) ? [] : $cases;
        $arr['link']=empty($link) ? [] : $link;
        $arr['message']=empty($message) ? [] : $message;
        $arr['teacher']=empty($teacher) ? [] : $teacher;
        return $arr;
    }

    //获取产品
    public static function getProduct($siteId){
        $pro = AllocationProductModel::where('site_id',$siteId)
            ->getHumpArray(['allocation_pro_id','table_id','product_type_name']);
        return $pro;
    }
    //获取单条
    public static function getProOne($allocationProId)
    {
        $pro = AllocationProductModel::where('allocation_pro_id',$allocationProId)->firstHumpArray(['*']);
        return $pro;
    }

    //获取新闻
    public static function getNews($siteId){
        $news = AllocationNewsModel::where('site_id',$siteId)
            ->getHumpArray(['allocation_news_id','table_id','news_type_name']);
        return $news;
    }
    //获取单条
    public static function getNewsOne($allocationNewsId)
    {
        $news = AllocationNewsModel::where('allocation_news_id',$allocationNewsId)->firstHumpArray(['*']);
        return $news;
    }

    //获取案例
    public static function getCases($siteId){
        $cases = AllocationCasesModel::where('site_id',$siteId)
            ->getHumpArray(['allocation_cases_id','table_id','cases_type_name']);
        return $cases;
    }
    //获取单条
    public static function getCasesOne($allocationCasesId)
    {
        $cases = AllocationCasesModel::where('allocation_cases_id',$allocationCasesId)->firstHumpArray(['*']);
        return $cases;
    }

    //获取友情链接
    public static function getLink($siteId){
        $link = AllocationLinkModel::where('site_id',$siteId)
            ->getHumpArray(['allocation_link_id','link_name']);
        return $link;
    }
    //获取单条
    public static function getLinkOne($allocationLinkId)
    {
        $link = AllocationLinkModel::where('allocation_link_id',$allocationLinkId)->firstHumpArray(['*']);
        return $link;
    }

    //获取留言
    public static function getMessage($siteId){
        $message = AllocationMessageModel::where('site_id',$siteId)
            ->getHumpArray(['allocation_message_id','message_name']);
        return $message;
    }

    //获取教师--后期处理多语言名称
    public static function getTeacher($siteId)
    {
        $res = [];
        if($siteId == 32){
            $teacher = [
                'allocationTeacherId'=>1,
                'teacherName'=>'师资团队'
            ];
            $res[]=$teacher;
        }
        return $res;
    }
    //获取单条
    public static function getMessageOne($allocationMessageId)
    {
        $message = AllocationMessageModel::where('allocation_message_id',$allocationMessageId)->firstHumpArray(['*']);
        return $message;
    }


    //添加主表
    public static function addSiteSeo ($data=[])
    {
        $model = new SiteSeoModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //修改seo
    public static function edit($data)
    {
        $type=$data['type'];
        $tableId=$data['tableId'];
        foreach ($data['lang'] as $key=>$value){
            self::editSeo($type,$tableId,$value);
        }

    }
    //修改
    public static function editSeo($type,$tableId,$data)
    {
        SiteSeoModel::whereSiteId()->where('type',$type)->where('table_id',$tableId)->where('language_id',$data['languageId'])->updateByHump($data);
        return;
    }
    //获取SEO
    public static function getSeoOne($data)
    {
        $seo = SiteSeoModel::whereSiteId()->where('type',$data['type'])->where('table_id',$data['tableId'])
            ->firstHumpArray(['type','seo_id','site_id','table_id']);
        //多语言
        $seo2 = SiteSeoModel::whereSiteId()->where('type',$data['type'])->where('table_id',$data['tableId'])
            ->getHumpArray(['language_id','site_title','site_keywords','site_description']);
        $seo['lang']=empty($seo2) ? [] : $seo2;
        return $seo;
    }

    //前端获取SEO
    public static function getSeoApi($data)
    {
        $seo = SiteSeoModel::whereSiteId()->where('type',$data['type'])->where('table_id',$data['tableId'])
            ->where('language_id',$data['languageId'])
            ->firstHumpArray(['*']);
        return $seo;
    }

    //默认
    public static function defaultSeo($data)
    {
        if(!empty($data)){
            foreach ($data as $item){
                self::getSeo($item['type'],$item['tableId'],$item['typeName']);
            }
        }
        return;
    }
    //获取SEO
    public static function getSeo($type,$tableId,$typeName)
    {
        $seo = SiteSeoModel::whereSiteId()->where('type',$type)->where('table_id',$tableId)->firstHumpArray();
        if(empty($seo)){
            $res = LanguageLogic::getSiteLanguage();
            foreach ($res as $value){
                if($value['languageId']==1){
                    //添加
                    self::addDefaultZH($type,$tableId,$value['languageId'],$typeName);
                }
                if($value['languageId']==2){
                    //添加
                    self::addDefaultEN($type,$tableId,$value['languageId']);
                }
            }
        }
        return;
    }
    public static function addDefaultZH($type,$tableId,$languageId,$typeName)
    {
        $res=[];
        if($type=='index'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>$typeName,
                'siteKeywords'=>$typeName,
                'siteDescription'=>$typeName,
            ];
        }
        if($type=='product'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>$typeName,
                'siteKeywords'=>$typeName,
                'siteDescription'=>$typeName,
            ];
        }
        if($type=='news'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>$typeName,
                'siteKeywords'=>$typeName,
                'siteDescription'=>$typeName,
            ];
        }
        if($type=='case'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>$typeName,
                'siteKeywords'=>$typeName,
                'siteDescription'=>$typeName,
            ];
        }
        if($type=='page'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>$typeName,
                'siteKeywords'=>$typeName,
                'siteDescription'=>$typeName,
            ];
        }
        self::addSiteSeo($res);
        return;
    }
    public static function addDefaultEN($type,$tableId,$languageId)
    {
        $res=[];
        if($type=='index'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>'seo title',
                'siteKeywords'=>'seo keywords',
                'siteDescription'=>'seo description',
            ];
        }
        if($type=='product'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>'seo title',
                'siteKeywords'=>'seo keywords',
                'siteDescription'=>'seo description',
            ];
        }
        if($type=='news'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>'seo title',
                'siteKeywords'=>'seo keywords',
                'siteDescription'=>'seo description',
            ];
        }
        if($type=='case'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>'seo title',
                'siteKeywords'=>'seo keywords',
                'siteDescription'=>'seo description',
            ];
        }
        if($type=='page'){
            $res = [
                'type'=>$type,
                'tableId'=>$tableId,
                'languageId'=>$languageId,
                'siteTitle'=>'seo title',
                'siteKeywords'=>'seo keywords',
                'siteDescription'=>'seo description',
            ];
        }
        self::addSiteSeo($res);
        return;
    }

    //获取内页列表
    public static function getPageLists($languageId)
    {
        $AboutLists = AboutModel::whereSiteId()
            ->where('about_description.language_id',$languageId)
            ->orderby('about.sort','DESC')
            ->leftjoin('about_description', 'about.about_id', '=', 'about_description.about_id')
            ->getHumpArray([
                'about.about_type',
                'about_description.about_title',
            ]);
        //拿原来的数据对比，看内页有没有被删除
        //$original = SiteSeoModel::whereSiteId()->getHumpArray(['type']);
        foreach ($AboutLists as $value) {
            $res = self::checkPage($value['aboutType']);
            if(empty($res)){
                self::defaultPage($value);
            }
        }
        return $AboutLists;
    }
    public static function checkPage($type)
    {
        $seo = SiteSeoModel::whereSiteId()->where('type',$type)->where('table_id',1)->firstHumpArray();
        return $seo;
    }
    public static function defaultPage($value)
    {
        $res = LanguageLogic::getSiteLanguage();
        foreach ($res as $item){
            if($item['languageId']==1){
                //添加
                $res = [
                    'type'=>$value['aboutType'],
                    'tableId'=>1,
                    'languageId'=>$item['languageId'],
                    'siteTitle'=>$value['aboutTitle'],
                    'siteKeywords'=>$value['aboutTitle'],
                    'siteDescription'=>$value['aboutTitle'],
                ];
                self::addSiteSeo($res);
            }
            if($item['languageId']==2){
                //添加
                $res = [
                    'type'=>$value['aboutType'],
                    'tableId'=>1,
                    'languageId'=>$item['languageId'],
                    'siteTitle'=>'seo title',
                    'siteKeywords'=>'seo keywords',
                    'siteDescription'=>'seo description',
                ];
                self::addSiteSeo($res);
            }
        }
        return;
    }

    //
    public static function getMenuTypeLists($languageId,$siteId,$classType)
    {
        $res=[];
        if($classType==1){
            $arr = self::getProLists($languageId,$siteId);
            foreach ($arr as $value) {
                $res1['classType']=1;
                $res1['tableId']=$value['tableId'];
                $res1['menuTypeName']=$value['productTypeName'];
                $res1['menuClassName']='product';
                $res[]=$res1;
            }
        }
        if($classType==2){
            $arr = self::getNewLists($languageId,$siteId);
            foreach ($arr as $value) {
                $res1['classType']=1;
                $res1['tableId']=$value['tableId'];
                $res1['menuTypeName']=$value['newsTypeName'];
                $res1['menuClassName']='news';
                $res[]=$res1;
            }
        }
        if($classType==3){
            $arr = self::getCaseLists($languageId,$siteId);
            foreach ($arr as $value) {
                $res1['classType']=1;
                $res1['tableId']=$value['tableId'];
                $res1['menuTypeName']=$value['casesTypeName'];
                $res1['menuClassName']='cases';
                $res[]=$res1;
            }
        }
        return $res;
    }
    public static function getProLists($languageId,$siteId)
    {
        $pro = AllocationProductModel::where('allocation_product.site_id',$siteId)
            ->where('allocation_pro_lang.language_id',$languageId)
            ->leftJoin('allocation_pro_lang','allocation_product.allocation_pro_id','=','allocation_pro_lang.allocation_pro_id')
            ->getHumpArray(['allocation_product.allocation_pro_id','allocation_product.table_id','allocation_pro_lang.product_type_name']);
        return $pro;
    }
    public static function getNewLists($languageId,$siteId)
    {
        $news = AllocationNewsModel::where('allocation_news.site_id',$siteId)
            ->where('allocation_news_lang.language_id',$languageId)
            ->leftJoin('allocation_news_lang','allocation_news.allocation_news_id','=','allocation_news_lang.allocation_news_id')
            ->getHumpArray(['allocation_news.allocation_news_id','allocation_news.table_id','allocation_news_lang.news_type_name']);
        return $news;
    }
    public static function getCaseLists($languageId,$siteId)
    {
        $cases = AllocationCasesModel::where('allocation_cases.site_id',$siteId)
            ->where('allocation_cases_lang.language_id',$languageId)
            ->leftJoin('allocation_cases_lang','allocation_cases.allocation_cases_id','=','allocation_cases_lang.allocation_cases_id')
            ->getHumpArray(['allocation_cases.allocation_cases_id','allocation_cases.table_id','allocation_cases_lang.cases_type_name']);
        return $cases;
    }

    //
    public static function getAllocationLists($siteId,$languageId)
    {
        $arr=[];
        if(empty($languageId)){
            //获取语言列表，获取第一个语言
            $res = LanguageLogic::getSiteLanguage();
            $languageId = $res[0]['languageId'];
        }
        $pro = self::getProLists($languageId,$siteId);
        $news = self::getNewLists($languageId,$siteId);
        $cases = self::getCaseLists($languageId,$siteId);
        $link = self::getLinkLists($siteId,$languageId);
        $message = self::getMessageLists($siteId,$languageId);
        $teacher = self::getTeacher($siteId);//-----------
        $arr['product']=empty($pro) ? [] : $pro;
        $arr['teacher']=empty($teacher) ? [] : $teacher;//-----------
        $arr['news']=empty($news) ? [] : $news;
        $arr['cases']=empty($cases) ? [] : $cases;
        $arr['link']=empty($link) ? [] : $link;
        $arr['message']=empty($message) ? [] : $message;
        return $arr;
    }
    //获取友情链接
    public static function getLinkLists($siteId,$languageId){
        $link = AllocationLinkModel::where('allocation_link.site_id',$siteId)
            ->where('allocation_link_lang.language_id',$languageId)
            ->leftJoin('allocation_link_lang','allocation_link.allocation_link_id','=','allocation_link_lang.allocation_link_id')
            ->getHumpArray(['allocation_link.allocation_link_id','allocation_link_lang.link_name']);
        return $link;
    }
    //获取留言
    public static function getMessageLists($siteId,$languageId){
        $message = AllocationMessageModel::where('allocation_message.site_id',$siteId)
            ->where('allocation_message_lang.language_id',$languageId)
            ->leftJoin('allocation_message_lang','allocation_message.allocation_message_id','=','allocation_message_lang.allocation_message_id')
            ->getHumpArray(['allocation_message.allocation_message_id','allocation_message_lang.message_name']);
        return $message;
    }

    /*
     * 获取产品配置
     * */
    public static function getAllocationProduct($siteId,$languageId)
    {
        $model = new AllocationProductModel();
        $res = $model->where('site_id',$siteId)->getHumpArray(['allocation_pro_id','site_id','table_id']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                $lang = self::getProLangOne($value['allocationProId'],$languageId);
                $res[$key]['languageId']=$lang['languageId'];
                $res[$key]['menuTypeName']=$lang['productTypeName'];
            }
        }
        return $res;
    }
    public static function getProLangOne($allocationProId,$languageId)
    {
        $pro = (new AllocationProLangModel())->where('allocation_pro_id',$allocationProId)
            ->where('language_id',$languageId)
            ->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取新闻配置
     * */
    public static function getAllocationNews($siteId,$languageId)
    {
        $model = new AllocationNewsModel();
        $res = $model->where('site_id',$siteId)->getHumpArray(['allocation_news_id','site_id','table_id']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                $lang = self::getNewsLangOne($value['allocationNewsId'],$languageId);
                $res[$key]['languageId']=$lang['languageId'];
                $res[$key]['menuTypeName']=$lang['newsTypeName'];
            }
        }
        return $res;
    }
    public static function getNewsLangOne($allocationNewsId,$languageId)
    {
        $pro = (new AllocationNewsLangModel())->where('allocation_news_id',$allocationNewsId)
            ->where('language_id',$languageId)
            ->firstHumpArray(['*']);
        return $pro;
    }
    /*
     * 获取新闻配置
     * */
    public static function getAllocationCases($siteId,$languageId)
    {
        $model = new AllocationCasesModel();
        $res = $model->where('site_id',$siteId)->getHumpArray(['allocation_cases_id','site_id','table_id']);
        if(!empty($res)){
            foreach ($res as $key=>$value){
                $lang = self::getCasesLangOne($value['allocationCasesId'],$languageId);
                $res[$key]['languageId']=$lang['languageId'];
                $res[$key]['menuTypeName']=$lang['casesTypeName'];
            }
        }
        return $res;
    }
    public static function getCasesLangOne($allocationCasesId,$languageId)
    {
        $pro = (new AllocationCasesLangModel())->where('allocation_cases_id',$allocationCasesId)
            ->where('language_id',$languageId)
            ->firstHumpArray(['*']);
        return $pro;
    }


}
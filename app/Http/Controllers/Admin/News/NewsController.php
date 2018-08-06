<?php

namespace App\Http\Controllers\Admin\News;

use App\Logic\News\NewsLogic;
use App\Logic\News\NewsCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Http\Middleware\SiteId;
use \App\Http\Controllers\Controller;

class NewsController extends Controller
{

    /*
     *  lang[
     *      languageId      语言ID
     *      newsTitle       名称
     *      newsAuthor      作者
     *      newsContent     内容
     *      newsDesc        描述内容
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *      photos[
     *          languageId          语言ID
     *          productImagePic     图片
     *          productImageDesc    图片描述或者标题
     *      ]
     *      contentFile[    编辑器文件
     *          galleryUrl          图片
     *      ]
     *      banner[
     *          languageId          语言ID
     *          productBannerPic     图片
     *          productBannerDesc    图片描述或者标题
     *      ]
     *  ]
     */
    //添加
    public function AddNews()
    {
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => '',//分类id
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'recommend' => '',//推荐
                'newsUrl' => 'no_required',//链接
                'lang'=>'',//语言数组--languageId、newsTitle、newsAuthor、newsContent、newsDesc、siteTitle、siteKeywords、siteDescription、photos、banner
                'newsThumb' => 'no_required',//缩略图
                'newsbanner'=> 'no_required',//广告图

            ]
            , 'POST');
        NewsLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getNewsLists(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => 'no_required',//分类id
                'newsTitle' => 'no_required',
                'languageId'=>  'no_required'
            ]
            , 'GET');
        $res = NewsLogic::getNewsList($this->verifyData,$this->verifyData['tableId']);//,$this->verifyData['tableId']

        return $res;
    }

    //获取单条
    public function getNewsOne(){
        $this->verify(
            [
                'newsId' => '',
            ]
            , 'GET');
        $res = NewsLogic::getNewsOne($this->verifyData['newsId']);//,$this->verifyData['tableId']

        return ['data'=>$res];
    }

    /*
         *  lang[
         *      languageId      语言ID
         *      newsTitle       名称
         *      newsAuthor      作者
         *      newsContent     内容
         *      newsDesc        描述内容
         *      siteTitle       seo站点标题
         *      siteKeywords    站点关键字
         *      siteDescription 描述
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
    public function editNews(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsId' => '',//新闻ID
                'newsCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'recommend' => '',//推荐
                'newsUrl' => 'no_required',//链接
                'newsThumb' => 'no_required',//缩略图
                'lang'=>'',//语言数组--languageId、newsTitle、newsAuthor、newsContent、newsDesc、siteTitle、siteKeywords、siteDescription、photos
                'newsbanner'=> 'no_required',//广告图
            ]
            , 'POST');
         NewsLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteNews(){
        $this->verify(
            [
                'newsId' => '',//新闻ID
            ]
            , 'POST');
        NewsLogic::delAffair($this->verifyData['newsId']);//,$this->verifyData['tableId']

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'newsId'=>'',
                'isOn' => ''
            ]
            , 'POST');
        $data['isOn']=$this->verifyData['isOn'];
        NewsLogic::isShow($data,$this->verifyData['newsId']);

        return;
    }

    //测试获取ID
    public function getCateId(){
        $this->verify(
            [
                'tableId'=>'',
                'newsCateId' => '',//新闻ID
            ]
            , 'POST');
        $res = NewsCateLogic::getCateId($this->verifyData['newsCateId'],$this->verifyData['tableId']);//,$this->verifyData['tableId']

        return ['data'=>$res];
    }

    //测试多套产品列表
    /*public function getProductTypeLists(){
        $siteId = SiteId::getSiteId();
        $arr['news']=[];
        $arr['product']=[];
        $arr['cases']=[];
        if($siteId==5){
            $res1['tableId']=1;
            $res1['productTypeName']='product1';
            $res2['tableId']=2;
            $res2['productTypeName']='product2';
            $res3['tableId']=3;
            $res3['productTypeName']='product3';
            $res4['tableId']=4;
            $res4['productTypeName']='product4';
            $res5['tableId']=5;
            $res5['productTypeName']='product5';
            $arr['product'][]=$res1;
            $arr['product'][]=$res2;
            $arr['product'][]=$res3;
            $arr['product'][]=$res4;
            $arr['product'][]=$res5;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='news1';
            $arr2['tableId']=2;
            $arr2['newsTypeName']='news2';
            $arr['news'][]=$arr1;
            $arr['news'][]=$arr2;
            $arr3['tableId']=1;
            $arr3['casesTypeName']='cases1';
            $arr4['tableId']=2;
            $arr4['casesTypeName']='cases2';
            $arr['cases'][]=$arr3;
            $arr['cases'][]=$arr4;
        }
        if($siteId==11){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $res2['tableId']=2;
            $res2['productTypeName']='厂房设备';
            $arr['product'][]=$res1;
            $arr['product'][]=$res2;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='新闻系统';
            $arr['news'][]=$arr1;
            $arr2['tableId']=1;
            $arr2['casesTypeName']='案例系统';
            $arr['cases'][]=$arr2;
        }
        if($siteId==13){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $arr['product'][]=$res1;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='新闻系统';
            $arr['news'][]=$arr1;
        }
        if($siteId==14){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $arr['product'][]=$res1;
            $res2['tableId']=2;
            $res2['productTypeName']='案例工程';
            $arr['product'][]=$res2;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='新闻系统';
            $arr['news'][]=$arr1;
        }
        if($siteId==16){
            $res1['tableId']=1;
            $res1['productTypeName']='产品系统';
            $arr['product'][]=$res1;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='学校信息';
            $arr2['tableId']=2;
            $arr2['newsTypeName']='教学科研';
            $arr3['tableId']=3;
            $arr3['newsTypeName']='德育之窗';
            $arr4['tableId']=4;
            $arr4['newsTypeName']='学科在线';
            $arr5['tableId']=5;
            $arr5['newsTypeName']='校园快讯';
            $arr6['tableId']=6;
            $arr6['newsTypeName']='校园文化';
            $arr7['tableId']=7;
            $arr7['newsTypeName']='学生园地';
            $arr8['tableId']=8;
            $arr8['newsTypeName']='社团活动';
            $arr9['tableId']=9;
            $arr9['newsTypeName']='本站推荐';
            $arr10['tableId']=10;
            $arr10['newsTypeName']='公告通知';
            $arr['news'][]=$arr1;
            $arr['news'][]=$arr2;
            $arr['news'][]=$arr3;
            $arr['news'][]=$arr4;
            $arr['news'][]=$arr5;
            $arr['news'][]=$arr6;
            $arr['news'][]=$arr7;
            $arr['news'][]=$arr8;
            $arr['news'][]=$arr9;
            $arr['news'][]=$arr10;
        }
        if($siteId==17){
            $res1['tableId']=1;
            $res1['productTypeName']='产品中心';
            $arr['product'][]=$res1;
            $arr1['tableId']=1;
            $arr1['newsTypeName']='新闻中心';
            $arr2['tableId']=2;
            $arr2['newsTypeName']='技术支持';
            $arr['news'][]=$arr1;
            $arr['news'][]=$arr2;
            $arr3['tableId']=1;
            $arr3['casesTypeName']='案例展示';
            $arr['cases'][]=$arr3;

        }
        return ['data'=>$arr];
    }*/


    //获取全部列表
    public function getNewsListsTest(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
                'newsCateId' => 'no_required',//分类id
                'newsTitle' => 'no_required',
                'languageId'=>  'no_required',
                'newsSort'=>  'no_required'
            ]
            , 'GET');
        $res = NewsLogic::getNewsListsTest($this->verifyData,$this->verifyData['tableId']);//,$this->verifyData['tableId']

        return $res;
    }
    //获取全部列表
    public function getNewsOneTest(){
        $this->verify(
            [
                'newsId'=>'',//
            ]
            , 'GET');
        $res = NewsLogic::getNewsOneTest($this->verifyData['newsId']);//,$this->verifyData['tableId']

        return $res;
    }



}

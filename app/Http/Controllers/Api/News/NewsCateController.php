<?php

namespace App\Http\Controllers\Api\News;

use App\Logic\News\NewsCateLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsCateController extends Controller
{

    //获取全部列表
    public function getNewsCateLists(){
        $this->verify(
            [
                'tableId'=>'',//第几套新闻，1、2、3、4、5
            ]
            , 'GET');
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $res = NewsCateLogic::getNewsCateList($data,$this->verifyData['tableId']);
        return ['lists'=>$res];
    }

    //获取单条
    public function getNewsCateOne(){
        $this->verify(
            [
                'newsCateId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsCateLogic::getNewsCate($this->verifyData['newsCateId'],$languageId);
        return ['data'=>$res];
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

    //获取下一级所有
    public function getNewsKids()
    {
        $this->verify(
            [
                'tableId' => '',//第几套-1、2、3、4、5产品
                'newsCateId' => '',//新闻ID
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsCateLogic::getNewsCateKids($this->verifyData['newsCateId'],$this->verifyData['tableId'],$languageId);
        return ['lists'=>$res];
    }

    //获取父级ID
    public function getParents(){
        $this->verify(
            [
                'tableId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NewsCateLogic::getParents($this->verifyData['tableId'],$languageId);
        return ['lists'=>$res];
    }

}

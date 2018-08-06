<?php

namespace App\Http\Controllers\Api\About;

use App\Logic\About\AboutCateLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AboutCateController extends Controller
{

    //获取全部列表
    public function getAboutCateLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $res = AboutCateLogic::getAboutCateList($data);

        return ['lists'=>$res];
    }

    //获取单条
    public function getAboutCateOne(){
        $this->verify(
            [
                'aboutCateId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = AboutCateLogic::getAboutCate($this->verifyData['aboutCateId'],$languageId);

        return ['data'=>$res];
    }


    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'aboutCateId' => '',
            ]
            , 'GET');
        $res = AboutCateLogic::getAboutCateId($this->verifyData['aboutCateId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'aboutCateId' => '',
            ]
            , 'GET');
        $res = AboutCateLogic::getChildId($this->verifyData['aboutCateId']);
        return ['data'=>$res];
    }


}

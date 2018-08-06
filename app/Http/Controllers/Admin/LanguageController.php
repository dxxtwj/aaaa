<?php

namespace App\Http\Controllers\Admin;

use App\Logic\LanguageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class LanguageController extends Controller
{


    //获取全部列表
    public function getLanguageLists(){

        $res = LanguageLogic::getLanguageList();
        return ['lists'=>$res];
    }

    //获取站点语言列表
    public function getLanguage(){

        $res = LanguageLogic::getSiteLanguage();

        return ['lists'=>$res];
    }

    //获取单条
    public function getLanguageOne(){
        $this->verify(
            [
                'languageId' => '',
            ]
            , 'GET');
        $res = LanguageLogic::getLanguageOne($this->verifyData['languageId']);

        return ['data'=>$res];
    }



}

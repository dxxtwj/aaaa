<?php

namespace App\Http\Controllers\V0\Admin;

use App\Logic\V0\LanguageLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    //添加
    public function AddLanguage()
    {
        $this->verify(
            [
                'languageTitle' => '',//语言名称
                'languageKey'=>'no_required',
            ]
            , 'POST');
        LanguageLogic::addLanguage($this->verifyData);

    }

    //获取全部列表
    public function getLanguageLists(){

        $res = LanguageLogic::getLanguageList();

        return ['lists'=>$res];
    }

    //获取两条---测试
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

    //修改
    public function editLanguage(){
        $this->verify(
            [
                'languageId' => '',
                'languageTitle'=>'no_required',
                'languageKey'=>'no_required',
            ]
            , 'POST');
        LanguageLogic::editLanguage($this->verifyData,$this->verifyData['languageId']);
        return;
    }
    //删除
    public function deleteLanguage(){
        $this->verify(
            [
                'languageId' => '',//新闻ID
            ]
            , 'POST');
        LanguageLogic::deleteLanguage($this->verifyData['languageId']);

        return;
    }


}

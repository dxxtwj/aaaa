<?php

namespace App\Http\Controllers\Admin\About;

use App\Logic\AboutLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AboutController extends Controller
{
    /*
     *  lang[
     *      aboutTitle      名称
     *      aboutContent    内容
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *      contentFile[    编辑器里面上传的图片
     *          galleryUrl 图片路径
     *      ]
     *  ]
     */
    //添加
    public function AddAbout()
    {
        $this->verify(
            [
                'isOn' =>'',
                'sort' =>'',
                'aboutType'=>'no_required',
                'aboutThumb'=>'no_required',
                'aboutbanner'=>'no_required',
                'lang' =>''
            ]
            , 'POST');
        AboutLogic::addAll($this->verifyData);
    }

    //获取全部列表
    public function getAboutLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = AboutLogic::getAboutList($this->verifyData);
        return ['lists'=>$res];
    }

    //获取aboutType列表
    public function getAboutType(){
        $res = AboutLogic::getAboutTypeList();
        return ['lists'=>$res];
    }

    //获取单条
    public function getAboutOne(){
        $this->verify(
            [
                'aboutId' => '',
            ]
            , 'GET');
        $res = AboutLogic::getAboutOne($this->verifyData['aboutId']);
        return ['data'=>$res];
    }

    //修改
    public function editAbout(){
        $this->verify(
            [
                'aboutId' => '',//ID
                'isOn' =>'',
                'sort' =>'',
                'aboutType'=>'no_required',
                'aboutThumb'=>'no_required',
                'aboutbanner'=>'no_required',
                'lang' =>''

            ]
            , 'POST');
        AboutLogic::editAll($this->verifyData);

        return;
    }
    //修改
    public function deleteAbout(){
        $this->verify(
            [
                'aboutId' => '',//新闻ID
            ]
            , 'POST');
        AboutLogic::delAffair($this->verifyData['aboutId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'aboutId' => '',
                'isOn' => ''
            ]
            , 'POST');
        AboutLogic::isShow($this->verifyData,$this->verifyData['aboutId']);

        return;
    }

    public function getAboutTest()
    {
        $res = AboutLogic::getAboutTest();
        return['data'=>$res];
    }


}

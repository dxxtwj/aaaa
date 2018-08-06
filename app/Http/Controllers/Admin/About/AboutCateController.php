<?php

namespace App\Http\Controllers\Admin\About;

use App\Logic\About\AboutCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AboutCateController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      aboutCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //添加
    public function AddAboutCate()
    {
        $this->verify(
            [
                'pid' => '',//分类id
                'isOn'=>'',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
                'aboutCateThumb' => 'no_required',//图片
            ]
            , 'POST');
        AboutCateLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getAboutCateLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = AboutCateLogic::getAboutCateList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getAboutCateOne(){
        $this->verify(
            [
                'aboutCateId' => '',
            ]
            , 'GET');
        $res = AboutCateLogic::getAboutCateOne($this->verifyData['aboutCateId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      AboutCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editAboutCate(){
        $this->verify(
            [
                'aboutCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
                'aboutCateThumb' => 'no_required',//图片
            ]
            , 'POST');
        AboutCateLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteAboutCate(){
        $this->verify(
            [
                'aboutCateId' => '',//新闻ID
            ]
            , 'POST');
        AboutCateLogic::deleteAboutCate($this->verifyData['aboutCateId']);

        return;
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

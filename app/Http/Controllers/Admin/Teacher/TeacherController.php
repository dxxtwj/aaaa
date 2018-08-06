<?php

namespace App\Http\Controllers\Admin\Teacher;

use App\Http\Middleware\SiteId;
use App\Logic\Teacher\TeacherLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TeacherController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      name            标题
     *      position        职业
     *      description     描述
     *      content         内容
     *      siteTitle       seo
     *      siteKeywords    seo
     *      siteDescription seo
     *  ]
     */
    //添加
    public function Add()
    {
        $this->verify(
            [
                'sort' => '',//排序
                'isOn' => 'no_required',//排序
                'recommend' => 'no_required',//排序
                'headimg' => 'no_required',//图片
                'lang'=>'no_required',//语言数组
            ]
            , 'POST');
        TeacherLogic::addAll($this->verifyData);
        return;
    }

    //获取全部列表
    public function getLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'name' => 'no_required',
            ]
            , 'GET');
        $res = TeacherLogic::getList($this->verifyData);
        return $res;
    }

    //获取全部列表
    public function getListsByName(){
        $this->verify(
            [
                'name' => 'no_required',
            ]
            , 'GET');
        $res = TeacherLogic::getListsByName($this->verifyData);
        return ['lists'=>$res];
    }

    //获取单条
    public function getOne(){
        $this->verify(
            [
                'teacherId' => '',
            ]
            , 'GET');
        $res = TeacherLogic::getTeacherOne($this->verifyData['teacherId']);
        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      name            标题
     *      position        职业
     *      description     描述
     *  ]
     */
    //修改
    public function edit(){
        $this->verify(
            [
                'teacherId' => '',//新闻ID
                'sort' => '',//排序
                'isOn' => 'no_required',//排序
                'recommend' => 'no_required',//排序
                'headimg' => 'no_required',//图片
                'lang'=>'no_required',//
            ]
            , 'POST');
        TeacherLogic::editAll($this->verifyData);
        return;
    }
    //删除
    public function delete(){
        $this->verify(
            [
                'teacherId' => '',//新闻ID
            ]
            , 'POST');
        TeacherLogic::delAffair($this->verifyData['teacherId']);
        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'teacherId' => '',
                'isOn' => ''
            ]
            , 'POST');
        TeacherLogic::isShow($this->verifyData,$this->verifyData['teacherId']);
        return;
    }



}

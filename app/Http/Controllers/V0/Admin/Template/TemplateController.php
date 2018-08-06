<?php

namespace App\Http\Controllers\V0\Admin\Template;

use App\Logic\V0\Template\TemplateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TemplateController extends Controller
{
    //添加
    public function AddTemplate()
    {
        $this->verify(
            [
                'templateCateId' => '',//分类id
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'recommend' => '',//推荐
                'templateTitle' => '',//标题
                'templateThumb' => '',//缩略图
                'templateDesc' => 'no_required',//描述
                'nuxtPath' => 'no_required',//模板路径
            ]
            , 'POST');
        TemplateLogic::addTemplate($this->verifyData);
    }

    //获取全部列表
    public function getTemplateLists(){
        $this->verify(
            [
                'templateCateId' => 'no_required',//分类id
                'templateTitle' => 'no_required',
            ]
            , 'GET');
        $res = TemplateLogic::getTemplateList($this->verifyData);

        return $res;
    }

    //获取单条
    public function getTemplateOne(){
        $this->verify(
            [
                'templateId' => '',
            ]
            , 'GET');
        $res = TemplateLogic::getTemplateOne($this->verifyData['templateId']);
        return ['data'=>$res];
    }

    public function editTemplate(){
        $this->verify(
            [
                'templateId' => '',//新闻ID
                'templateCateId' => '',//分类id
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'recommend' => '',//推荐
                'templateTitle' => '',//标题
                'templateThumb' => '',//缩略图
                'templateDesc' => 'no_required',//描述
                'nuxtPath' => 'no_required',//模板ID
            ]
            , 'POST');
        TemplateLogic::editTemplate($this->verifyData);

        return;
    }
    //删除
    public function deleteTemplate(){
        $this->verify(
            [
                'templateId' => '',//新闻ID
            ]
            , 'POST');
        TemplateLogic::deleteTemplate($this->verifyData['templateId']);

        return;
    }


   /*
    * 是否显示*/
    public function isShow(){
        $this->verify(
            [
                'templateId' => '',//新闻ID
                'isOn' => '',//新闻ID
            ]
            , 'POST');
        TemplateLogic::isShow($this->verifyData);
        return;
    }


}

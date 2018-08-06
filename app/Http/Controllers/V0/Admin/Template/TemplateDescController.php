<?php

namespace App\Http\Controllers\V0\Admin\Template;


use App\Logic\V0\Template\TemplateDescLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class TemplateDescController extends Controller
{
    /*
     * 'template':{
		{
			'name' :'xxxx',
			'url' :'cccccxxxx',
			'template_id' :'1111',
		},
		{
			'name' :'xxxx',
			'url' :'cccccxxxx',
			'template_id' :'1111',
		}
	}*/
    //添加
    public function AddTemplateDesc()
    {
        $this->verify(
            [
                'template' => '',//数组templateId/name/url/modelId/codeId/code
            ]
            , 'POST');

        TemplateDescLogic::add($this->verifyData['template']);
        return;
    }

    //获取全部列表
    public function getTemplateDescLists(){
        $this->verify(
            [
                'templateId' => '',//是否显示--暂时没有
                'isOn' => 'no_required',//是否显示--暂时没有
                'name' => 'no_required',//名称
                'url' => 'no_required',//地址
            ]
            , 'GET');
        $res = TemplateDescLogic::getDescListByTemplateId($this->verifyData);

        return $res;
    }

    //获取单条
    public function getTemplateDescOne(){
        $this->verify(
            [
                'templateDescId' => '',//是否显示
            ]
            , 'GET');
        $res = TemplateDescLogic::getTemplateDescOne($this->verifyData);
        return ['data'=>$res];
    }

    //url 获取模板
    public function getTemplateDescUrl(){
        $this->verify(
            [
                'url' => '',//是否显示
            ]
            , 'GET');
        $res = TemplateDescLogic::getTemplateDescUrl($this->verifyData['url']);
        return ['data'=>$res];
    }

    //编辑
    public function editTemplateDesc()
    {
        $this->verify(
            [
                'templateDescId' => '',
                'name' => '',//名称
                'url'=>'',//链接
                'modelId'=>'',
                'codeId'=>'',
                'code'=>''
            ]
            , 'POST');
        TemplateDescLogic::editTemplateDesc($this->verifyData);

    }

    //删除
    public function deleteTemplateDesc()
    {
        $this->verify(
            [
                'templateDescId' => '',
            ]
            , 'POST');
        TemplateDescLogic::deleteTemplateDesc($this->verifyData);

    }



}

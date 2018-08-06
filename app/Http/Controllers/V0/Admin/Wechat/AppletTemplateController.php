<?php

namespace App\Http\Controllers\V0\Admin\Wechat;

use App\Logic\Exception;
use App\Logic\V0\Wechat\AppletTemplateLogic;
use EasyWeChat\Factory;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class AppletTemplateController extends Controller
{
    /**
     * 添加小程序模板
     * */
    public function add()
    {
        $this->validate(null, [
            'templateId' =>'required|integer',
            'version' =>'required|string',
            'desc' => 'required|string',
        ]);
        AppletTemplateLogic::add($this->verifyData);
        return;
    }

    /**
     * 获取全部列表
     * */
    public function getLists(){
        $res = AppletTemplateLogic::getList();
        return $res;
    }

    /**
     * 获取单条
     * */
    public function getOne(){
        $this->verify(
            [
                'appletTemplateId' => '',
            ]
            , 'GET');
        $res = AppletTemplateLogic::getOne($this->verifyData['appletTemplateId']);
        return ['data'=>$res];
    }

    /**
     * 修改
     * */
    public function edit()
    {
        $this->validate(null, [
            'appletTemplateId'=>'required|string',
            'templateId' =>'required|integer',
            'version' => 'required|string',
            'desc' => 'required|string',
        ]);
        AppletTemplateLogic::edit($this->verifyData);
        return;
    }
    /**
     * 删除
     * */
    public function delete(){
        $this->verify(
            [
                'appletTemplateId' => '',
            ]
            , 'POST');
        AppletTemplateLogic::delete($this->verifyData['appletTemplateId']);
        return;
    }


}

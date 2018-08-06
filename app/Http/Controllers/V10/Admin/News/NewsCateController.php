<?php

namespace App\Http\Controllers\V10\Admin\News;

use App\Logic\V10\News\NewsCateLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class NewsCateController extends Controller
{
    //添加
    public function add()
    {
        $this->verify(
            [
                'pid' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'newsCateTitle'=>'',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        NewsCateLogic::add($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $lists = NewsCateLogic::Lists();
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'newsCateId' => '',//语言名称
            ]
            , 'GET');
        $data = NewsCateLogic::getOne($this->verifyData['newsCateId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'newsCateId' => '',
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'recommend'=>'no_required',
                'newsCateTitle'=>'no_required',
                'seoTitle'=>'no_required',
                'seoKeywords'=>'no_required',
                'seoDescription'=>'no_required',
            ]
            , 'POST');
        NewsCateLogic::edit($this->verifyData);
        return;
    }

    public function delete(){
        $this->verify(
            [
                'newsCateId' => '',
            ]
            , 'POST');
        NewsCateLogic::delete($this->verifyData['newsCateId']);
        return;
    }

}

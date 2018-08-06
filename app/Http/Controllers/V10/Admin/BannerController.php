<?php

namespace App\Http\Controllers\V10\Admin;

use App\Logic\V10\BannerLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class BannerController extends Controller
{
    //添加
    public function AddBanner()
    {
        $this->verify(
            [
                'image' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'url'=>'no_required'
            ]
            , 'POST');
        BannerLogic::addBanner($this->verifyData);
        return;
    }

    //列表
    public function getLists()
    {
        $lists = BannerLogic::Lists();
        return ['lists'=>$lists];
    }

    //单条
    public function getOne()
    {
        $this->verify(
            [
                'bannerId' => '',//语言名称
            ]
            , 'GET');
        $data = BannerLogic::getOne($this->verifyData['bannerId']);
        return ['data'=>$data];
    }


    //修改密码
    public function edit(){
        $this->verify(
            [
                'bannerId' => '',//语言名称
                'image' => '',//语言名称
                'isOn'=>'no_required',
                'sort'=>'no_required',
                'url'=>'no_required'
            ]
            , 'POST');
        BannerLogic::edit($this->verifyData);
        return;
    }


    public function delete(){
        $this->verify(
            [
                'bannerId' => '',
            ]
            , 'POST');
        BannerLogic::delete($this->verifyData['bannerId']);
        return;
    }


}

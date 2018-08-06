<?php

namespace App\Http\Controllers\Shopping\Admin\Home;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Home\HomeLogic;

class HomeController extends Controller
{
    /*
     * 添加首页表图片
     */
    public function addHome() {

        $this->verify(
            [
                'homeImg' => '',//  图片url
                'goodsId' => 'no_required',
                'homeName' => 'no_required',
                'homeOrder' => 'no_required',
                'homeContents' => 'no_required',
                'shopId' => 'no_required',
                'homeType' => '',//等于1表示轮播图  2 表示今日推荐
            ]
            , 'POST');
        HomeLogic::addHome($this->verifyData);
        return;
    }
    /*
     * 修改首页
     */
    public function editHome() {

        $this->verify(
            [
                'homeId' => '',//首页表ID
                'homeImg' => '',//  图片url
                'goodsId' => 'no_required',
                'homeName' => 'no_required',
                'homeOrder' => 'no_required',
                'homeContents' => 'no_required',
                'shopId' => 'no_required',
                'homeType' => '',//等于1表示轮播图  2 表示今日推荐
            ]
            , 'POST');
        HomeLogic::editHome($this->verifyData);
        return;
    }

    /*
     * 查询
     */
    public function showHome() {
        $this->verify(
            [
                'homeId' => 'no_required',//ID,传则查单条
                'homeType' => 'no_required',//类型
                'shopId' => 'no_required',//类型
            ]
            , 'POST');
        $res = HomeLogic::showHome($this->verifyData);
        return $res;
    }

    /*
     * 删除
     */
    public function deleteHome() {
        $this->verify(
            [
                'homeId' => '',//轮播图ID,传则查单条
            ]
            , 'POST');

        HomeLogic::deleteHome($this->verifyData['homeId']);
        return;
    }
}

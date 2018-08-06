<?php

namespace App\Http\Controllers\Shopping\Api\Home;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Home\HomeLogic;
use App\Logic\Shopping\Api\Order\OrderLogic;

class HomeController extends Controller
{

    /*
     * 前台首页表显示数据
     */
    public function homeType() {

        $this->verify(
            [
                'shopId' => '',
            ]
            , 'GET');
        $res = HomeLogic::homeType($this->verifyData['shopId']);
        return $res;
    }

    /*
     * 前台首页表显示数据
     */
    public function homeGoods() {

        $this->verify(
            [
                'shopId' => '',
            ]
            , 'GET');
        $res = HomeLogic::homeGoods($this->verifyData['shopId']);
        return $res;
    }

    /*
     * 前台首页表显示数据
     */
    public function homeBroadcast() {

        $this->verify(
            [
                'shopId' => '',
            ]
            , 'GET');
        $res = HomeLogic::homeBroadcast($this->verifyData['shopId']);
        return $res;
    }

    /*
     * 前台首页表显示数据
     */
    public function homeRecommend() {

        $this->verify(
            [
                'shopId' => '',
            ]
            , 'GET');
        $res = HomeLogic::homeRecommend($this->verifyData['shopId']);
        return $res;
    }

    /*
    * 前台首页表显示数据
    */
    public function homeNotice() {

        $this->verify(
            [
//                'shopId' => '',
            ]
            , 'GET');
        $res = HomeLogic::homeNotice();
        return $res;
    }

    public function login($name='userId',$val='') {
        if ($name != 'userId') {

            \Session::put($name, $val);

        } elseif ($name == 'userId') {//模拟登录  ->  给赵宁的

            \Session::put('userId', 9);

        }
//        \Session::put('shopId', 13246558413);
//        var_dump(\Session::get('userId'));
    }


}

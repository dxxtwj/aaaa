<?php

namespace App\Http\Controllers\Shangrui\Api\Home;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Home\HomeLogic;
use App\Logic\Shangrui\Api\Order\OrderLogic;

class HomeController extends Controller
{
    //首页热门推荐商品
    public function homeGoods(){
       $res = HomeLogic::homeGoods();
       return $res;
    }
    //首页新品推荐商品
    public function homeNewGoods(){
        $res = HomeLogic::homeNewGoods();
        return $res;
    }
    //首页轮播图
    public function homeHome(){
        $res = HomeLogic::homeHome();
        return $res;
    }
    // 前台首页类别名称显示
    public function showType(){
        $res = HomeLogic::showType();
        return $res;
    }
    // 前台首页推荐类别显示
    public function homeRecommendType(){

        $res = HomeLogic::homeRecommendType();
        return $res;
    }

    // 模拟前台登录
    public function login($name='userId',$val='') {
        if ($name != 'userId') {

            \Session::put($name, $val);

        } elseif ($name == 'userId') {//模拟登录  ->  给赵宁的

            \Session::put('userId', 1);

        }

//        var_dump(\Session::get('userId'));
    }

}

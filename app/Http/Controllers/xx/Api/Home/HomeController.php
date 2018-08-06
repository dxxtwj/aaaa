<?php

namespace App\Http\Controllers\Shangrui\Api\Home;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Home\HomeLogic;
use App\Logic\Shangrui\Api\Order\OrderLogic;

class HomeController extends Controller
{
    public function login($name='userId',$val='') {
        if ($name != 'userId') {

            \Session::put($name, $val);

        } elseif ($name == 'userId') {//模拟登录  ->  给赵宁的

            \Session::put('userId', 1);
//            var_dump(\Session::get('userId'));
        }
    }
}
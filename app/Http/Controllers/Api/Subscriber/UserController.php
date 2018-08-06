<?php

namespace App\Http\Controllers\Api\Subscriber;

use App\Logic\Subscriber\UserLogic;
use App\Logic\Subscriber\LoginLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class UserController extends Controller
{

    //获取用户信息
    public function getUserOne(){
        $uid=LoginLogic::getUid();
        $res = UserLogic::getinfo($uid);
        return ['data'=>$res];
    }




}

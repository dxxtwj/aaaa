<?php

namespace App\Http\Controllers\V0\Admin\Join;

use App\Logic\V0\Join\JoinLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class JoinController extends Controller
{
    //加入我们列表
    public function JoinLists()
    {
        $this->verify(
            [
                'phone' => 'no_required',//手机
            ]
            , 'GET');
        $res = JoinLogic::JoinLists($this->verifyData);
        return $res;
    }

    //加入我们单条
    public function JoinOne()
    {
        $this->verify(
            [
                'joinId' => '',//手机
            ]
            , 'GET');
        $res = JoinLogic::JoinOne($this->verifyData);
        return['lists'=>$res];
    }

}

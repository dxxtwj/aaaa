<?php

namespace App\Http\Controllers\V0\Admin\User;

use App\Logic\V0\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class UserController extends Controller
{
    //用户列表
    public function UserLists()
    {
        $this->verify(
            [
                'phone' => 'no_required',//手机
            ]
            , 'GET');
        $res = UserLogic::UserLists($this->verifyData);
        return $res;
    }

    //用户单条
    public function UserOne()
    {
        $this->verify(
            [
                'uid' => '',//手机
            ]
            , 'GET');
        $res = UserLogic::getUserById($this->verifyData['uid']);
        return ['data'=>$res];
    }


}

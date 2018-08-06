<?php

namespace App\Http\Controllers\Shopping\Api\User;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\User\UserLogic;

class UserController extends Controller
{
    /*
     * 查询用户信息
     */
    public function showUser() {
        $res = UserLogic::showUser();
        return $res;
    }

    /*
     * 用户添加意见反馈
     */
    public function addMessage(){
        $this->verify(
            [
                'userMessageContent' => 'no_required', //意见反馈
            ]
            ,'POST');
        $res = UserLogic::addMessage($this->verifyData);
        return $res;
    }


}

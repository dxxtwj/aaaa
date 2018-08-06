<?php

namespace App\Http\Controllers\Shopping\Admin\User;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\User\UserLogic;

class UserController extends Controller
{

    public function showUser() {

        $this->verify(
            [
                'userId' => 'no_required',
            ]
            , 'POST');
        $res = UserLogic::showUser($this->verifyData);
        return $res;
    }

    /*
     *  后台查询意见反馈
     */
    public function showUserMessage() {
        $res = UserLogic::showUserMessage();
        return $res;
    }

    /*
     * 后台删除意见反馈
     */
    public function deleteUserMessage(){
        $this->verify(
            [
                'userMessageId' => '',
            ]
            ,'POST');
        $res = UserLogic::deleteUserMessage($this->verifyData);
        return $res;
    }

}

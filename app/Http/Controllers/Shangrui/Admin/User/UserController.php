<?php

namespace App\Http\Controllers\Shangrui\Admin\User;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Admin\User\Userlogic;

class UserController extends Controller
{

    /*
     *  后台查询意见反馈
     */
    public function showUserMessage() {
        $this->verify(
            [
                'userId' => '',
            ]
            ,'GET');

        $res = UserLogic::showUserMessage($this->verifyData);
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

<?php

namespace App\Http\Controllers\V0\Api\User;

use App\Logic\V0\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Mail;

class UserController extends Controller
{
    //获取用户信息
    public static function getUser()
    {
        $res = UserLogic::getUserByLogin();
        if(empty($res['headimg'])){
            $res['headimg']='http://autostation-oss.oss-cn-shenzhen.aliyuncs.com//upload/other/4e1839D3CA183b4fe4A6ECE419.png';
        }
        return ['data'=>$res];
    }

    //修改用户信息
    public function editUser()
    {
        $this->verify(
            [
                'nickname' => 'no_required',
                'headimg' => 'no_required',
                'sex' => '',
                'birthday' => 'no_required',
                'email' => 'no_required',
            ]
            , 'POST');
        $uid = UserLogic::getUid();
        UserLogic::editUser($this->verifyData,$uid);
        return;
    }

    //修改密码
    public function editPassword()
    {
        $this->validate(null, [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string',
            'conPassword' => 'required|string',
            'codeImg' => 'required|string',
            'codeSms' => 'required|string',
        ]);
        UserLogic::editPassword($this->verifyData);
        return;
    }

    //忘记密码、重置密码
    public function resetPassword()
    {
        $this->validate(null, [
            'phone' => 'required|integer',
            'codeSms' => 'required|string',
        ]);
        UserLogic::resetPassword($this->verifyData);
        return;
    }

    //check用户是否存在
    public function checkPhone()
    {
        $this->validate(null, [
            'phone' => 'required|integer',
        ]);
        UserLogic::checkPhone($this->verifyData['phone']);
    }

}

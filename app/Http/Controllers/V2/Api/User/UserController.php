<?php

namespace App\Http\Controllers\V2\Api\User;

use App\Logic\V2\Common\VerifyLogic;
use App\Logic\V2\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\LoginLogic;
use Mail;

class UserController extends Controller
{
    //获取用户信息
    public static function getUser()
    {
        $res = UserLogic::getUserByLogin();
        if(!empty($res)){
            if(empty($res['userNickname'])){
                $res['userNickname']=$res['account'];
            }
            if(empty($res['userAvatar'])){
                $res['userAvatar']='http://automakesize-oss.oss-cn-shenzhen.aliyuncs.com//upload/other/d5514a3E01db5065f08FE655C2.jpg';
            }
        }
        return ['data'=>$res];
    }

    //修改用户信息
    public function editUser()
    {
        $this->verify(
            [
                'userName' => 'no_required',
                'userNickname' => 'no_required|string',
                'userAvatar' => 'no_required|string',
                'userHeight' => 'no_required|string',
                'userWeight' => 'no_required|string',
                'userMeasurements' => 'no_required|string',
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
        ]);
        UserLogic::editPassword($this->verifyData);
    }

    //忘记密码验证码验证
    public function forgetVerify()
    {
        $this->verify(
            [
                'account' => '',
                'type' => '',//1-name、2手机号码、3邮箱
                'codeImg' => '',
                'code' => '',
            ]
            , 'POST');
        UserLogic::forgetVerify($this->verifyData);
    }
    //忘记密码
    public function forgetPassword()
    {
        $this->verify(
            [
                'account' => '',
                'type' => '',//1-name、2手机号码、3邮箱
                'newPassword' => '',
                'conPassword' => '',
            ]
            , 'POST');
        UserLogic::forgetPassword($this->verifyData);
        return;
    }

    //绑定手机
    public function bindPhone()
    {
        $this->validate(null, [
            'userPhone' => 'required|integer',
            'codeImg' => 'required',
            'code' => 'required|integer',
        ]);
        UserLogic::bindPhone($this->verifyData);
        return;
    }

    //绑定手机
    public function bindEmail()
    {
        $this->validate(null, [
            'userEmail' => 'required',
            'codeImg' => 'required',
            'code' => 'required|integer',
        ]);
        UserLogic::bindEmail($this->verifyData);
        return;
    }


}

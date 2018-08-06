<?php

namespace App\Http\Controllers\User;

use App\Logic\Common\VerifyLogic;
use App\Logic\User\AccountLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\LoginLogic;

class LoginController extends Controller
{

    //登录
    public function accountLogin(Request $request)
    {
        $this->verify(
            [
                'account' => '',
                'password' => '',
                'verifyGuid'=> '',
                'imgVerify'=> ''
            ]
            , 'POST');
        $input = $this->verifyData;

        VerifyLogic::checkImgVerify('login.1', $input['verifyGuid'], $input['imgVerify']);

        //notCiphertext
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        // var_dump($request->getTrustedProxies());
        $res = LoginLogic::accountLogin($input['account'], $input['password'], $request->getClientIp());

        return [
            'data'=>$res
        ];
    }
    public function smsLogin(Request $request){
        $this->verify(
            [
                'verifyGuid' => '',
                'phone' => '',
                'smsVerify' => ''
            ]
            , 'POST');
        $input = $this->verifyData;


        // 验证短信验证码
        VerifyLogic::checkSmsVerify('login.1', $input['verifyGuid'], $input['phone'], $input['smsVerify']);
        //notCiphertext
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        // var_dump($request->getTrustedProxies());
        $res = LoginLogic::smsLogin($input['phone'], $request->getClientIp());
        return [
            'data'=>$res
        ];

    }
    public function isLogin(){
//        throw new RJsonError('sdsdsd', 'NO_LOGIN');
//        if (!LoginLogic::isLogin()){
//            throw new RJsonError('没有登录', 'NO_LOGIN');
//        }
        return ['data'=>[
            'isLogin'=>true
        ]];
    }
    /*
     * 退出登陆
     */
    public function logout(){
        LoginLogic::logout();
        return ['data'=>[]];
    }

    //注册
    public function getImgVerify()
    {
        $this->verify(
            [
                'verifyGuid' => ''
            ]
            , 'GET');


        $code = VerifyLogic::generateVerifyCode(4);
        $imageDataBase64 = VerifyLogic::getImgVerifyBase64('login.1', $this->verifyData['verifyGuid'], $code);
        return [
            'data'=>[
                'base64'=>$imageDataBase64
            ]
        ];
    }

    //发送验证码
    public function sendSms()
    {
        $this->verify(
            [
                'phone' => '',
                'verifyGuid' => '',
                'imgVerify' => ''
            ]
            , 'POST');
        // 验证图形验证码
        VerifyLogic::checkImgVerify('login.1', $this->verifyData['verifyGuid'], $this->verifyData['imgVerify']);
        // 查询是否存在手机号码
        AccountLogic::getOneByPhone($this->verifyData['phone'],['uid']);
        // 保存并且发送短信
        VerifyLogic::saveAndSendSmsVerify('login.1', $this->verifyData['verifyGuid'], $this->verifyData['phone'], [], 'SMS_84415005');

        return;
    }
}

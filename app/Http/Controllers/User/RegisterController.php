<?php

namespace App\Http\Controllers\User;

use App\Logic\Common\VerifyLogic;
use App\Logic\User\AccountLogic;
use App\Logic\User\RegisterLogic;
use App\Logic\User\UserPhoneRegisteringLogic;
use App\Model\UserPhoneRegisteringModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\InfoLogic;
use \App\Logic\User\LoginLogic;

class RegisterController extends Controller
{
    //注册
    public function getImgVerify()
    {
        $this->verify(
            [
                'verifyGuid' => ''
            ]
            , 'GET');


        $code = VerifyLogic::generateVerifyCode(4);
        /*var_dump($code);*/
        $imageDataBase64 = VerifyLogic::getImgVerifyBase64('register.1', $this->verifyData['verifyGuid'], $code);
        return [
            'data'=>[
                'base64'=>$imageDataBase64
            ]
        ];
    }

    //发送验证码
    public function register(Request $request)
    {
        $this->verify(
            [
                'verifyGuid' => '',
                'phone' => '',
                'smsVerify' => '',
                'password' => ''//,
                // 'account' => ''
            ]
            , 'POST');
        $input = $this->verifyData;
        // 验证短信验证码
        VerifyLogic::checkSmsVerify('register.1', $input['verifyGuid'], $input['phone'], $input['smsVerify']);
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));

        $data = RegisterLogic::phoneAndUsernameReg($input['phone'], $input['password'], $request->getClientIp(), empty($_POST['username'])?'':$_POST['username']);

        \Session::put('uid', $data['uid']);
        return ['data'=>$data];
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
//        // 验证图形验证码
//        VerifyLogic::checkImgVerify('register.1', $this->verifyData['verifyGuid'], $this->verifyData['imgVerify']);
//        try{
//            // 查询是否存在手机号码
//            AccountLogic::getOneByPhone($this->verifyData['phone'],['uid']);
//            throw new RJsonError('手机号码已经注册', 'PHONE_REGISTERED');
//        }catch (RJsonError $e){
//            if ($e->getErrorId()!=='NOT_FIND_ACCOUNT'){
//                throw $e;
//            }
//        }
        // 保存并且发送短信
        VerifyLogic::saveAndSendSmsVerify('register.1', $this->verifyData['verifyGuid'], $this->verifyData['phone'], [], 'SMS_84415005');

//        UserPhoneRegisteringLogic::addPhone($this->verifyData['phone']);
        return;
    }
}

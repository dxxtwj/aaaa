<?php

namespace App\Http\Controllers\V0\Api\User;

use App\Logic\V0\Common\VerifyLogic;
use App\Logic\V0\User\UserLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    //注册
    public function register()
    {
        $this->verify(
            [
                'keyId' => '',
                'password' => '',
                'code' => ''
            ]
            , 'POST');
        UserLogic::Account($this->verifyData);
        return;
    }

    //判断是否存在
    public function existAccount()
    {
        $this->verify(
            [
                'nickname' => 'no_required',
                'phone' => '',
                'codeImg'=>'',
            ]
            , 'POST');
        $string = UserLogic::existAccount($this->verifyData);
        return ['data'=>['keyId'=>$string]];
    }

    //获取图片验证码
    public function getImgVerify()
    {
        $code = VerifyLogic::generateVerifyCode(4);
        $image = VerifyLogic::getImageBase64($code);
        return [
            //'code'=>$code,
            'data'=>[
                'base64'=>$image
            ]
        ];
    }

    //发送验证码
    public function sendSms(Request $request)
    {
        $this->validate(null, [
            'phone' => 'required|integer',
        ]);
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        $code = VerifyLogic::generateVerifyCode(4, '0123456789');
        $data['code']=$code;
        //保存验证码
        VerifyLogic::addCode($this->verifyData['phone'],$code,1);
        //发送短信
        VerifyLogic::sendSmsVerify($this->verifyData['phone'],$data);
        return;
    }

    //获取邮箱的验证码
    public function getEmail()
    {
        $this->verify(
            [
                'email' => ''
            ]
            , 'POST');
        $data['code']=VerifyLogic::generateVerifyCode(4,'0123456789');
        //保存session
        \Session::put(['email'=>$this->verifyData['email']]);
        \Session::put(['emailCode'=>$data['code']]);
        $templateCode='emails.verifyCode';
        //获取发送人信息，配置邮箱配置
        //修改邮箱配置
        VerifyLogic::mailDeploy();
        /*\Config::set('mail.from', array('address' => '13592957850@163.com', 'name' => 'Name'));
        \Config::set('mail.username', '13592957850@163.com');
        \Config::set('mail.password', 'czs123456');*/
        $subject='获取验证码';
        VerifyLogic::sendEmailVerify($this->verifyData['email'],$data,$templateCode,$subject);
        return [
            'data'=>[]
        ];
    }

    //获取邮箱的验证码
    public function getEmailAnnex()
    {
        $this->verify(
            [
                'email' => ''
            ]
            , 'POST');
        $templateCode='emails.article';
        $data['article']='感谢您的参与，请查看附件了解相关信息。';
        //获取发送人信息，配置邮箱配置
        //修改邮箱配置
        VerifyLogic::mailDeploy();
        /*\Config::set('mail.from', array('address' => '13592957850@163.com', 'name' => 'Name'));
        \Config::set('mail.username', '13592957850@163.com');
        \Config::set('mail.password', 'czs123456');*/
        $subject='报名参赛反馈';
        VerifyLogic::sendEmailAnnex($this->verifyData['email'],$data,$templateCode,$subject);
        return [
            'data'=>[]
        ];
    }




}

<?php

namespace App\Http\Controllers\Api\Subscriber;

use App\Logic\Subscriber\UserLogic;
use App\Logic\Subscriber\LoginLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class LoginController extends Controller
{


    //获取全部列表
    public function login(){
        $this->verify(
            [
                'account' => '',
                'type'=>'',//1-name,2-phone,3-email
                'userPhone'=>'no_required',//type=1,已绑定过，用手机登录
                'password'=>'no_required',////密码--当为type=2为phone时可以为空-直接手机短信注册，看网站需求
                'code'=>'no_required',
            ]
            , 'POST');
        if($this->verifyData['type']==1){
            if(!empty($this->verifyData['userPhone'])){
                if(empty($this->verifyData['code'])){
                    throw new RJsonError('验证码', 'CODE_ERROR');
                }
            }elseif(empty($this->verifyData['password'])){
                throw new RJsonError('请输入密码', 'PASSWORD_ERROR');
            }
        }
        if($this->verifyData['type']==2){
            if(empty($this->verifyData['code'])){
                throw new RJsonError('验证码', 'CODE_ERROR');
            }
        }
        if($this->verifyData['type']==3){
            if(empty($this->verifyData['password'])){
                throw new RJsonError('请输入密码', 'PASSWORD_ERROR');
            }
        }
        $res = LoginLogic::Login($this->verifyData);
        return ['data'=>$res];
    }

    //登录状态
    public function isLogin(){
        if (!LoginLogic::isLogin()){
            return ['data'=>[
                'isLogin'=>false
            ]];
        }else{
            return ['data'=>[
                'isLogin'=>true
            ]];
        }
    }

    //查是否有这个账号
    public function getAccount(){
        $this->verify(
            [
                'account'=>'',
            ]
            , 'POST');
        LoginLogic::getAccount($this->verifyData['account']);
    }

    /*
     *  security[
     *      securityCateId      问题ID
     *      userSecurityTitle   问题标题
     *  ]
     */
    //密保
    public function securityAnswer()
    {
        $this->verify(
            [
                'account'=>'',
                'type'=>'',
                'security'=>'no_required',
                'code'=>'no_required',
            ]
            , 'POST');
        if($this->verifyData['type']==1){
            if(empty($this->verifyData['security'])){
                throw new RJsonError('请输入保密内容', 'SECURITY_ERROR');
            }
        }
        if($this->verifyData['type']!=1){
            if(empty($this->verifyData['code'])){
                throw new RJsonError('请输入验证码', 'CODE_ERROR');
            }
        }
        $res = LoginLogic::securityAnswer($this->verifyData);
        return ['data'=>$res];
    }

    //重置密码
    public function resetPassword(){
        $this->verify(
            [
                'uid'=>'',
                'account' => '',
                'newsPassword'=>'',
                'confirmPassword'=>''
            ]
            , 'POST');
        $res = LoginLogic::resetPassword($this->verifyData);
        return;
    }

    //退出登陆
    public function logout(){
        $data = LoginLogic::logout();
        return ['data'=>$data];
    }

}

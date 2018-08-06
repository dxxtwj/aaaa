<?php

namespace App\Http\Controllers\Api\Subscriber;

use App\Logic\Subscriber\UserLogic;
use App\Logic\Subscriber\UserSecurityLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class RegisterController extends Controller
{

    /*
     *  security[
     *      securityCateId  语言ID
     *      securityTitle   问题回答
     *  ]
     */
    //获取全部列表
    public function addRegister(){
        $this->verify(
            [
                'account'=>'',
                'type'=>'',//1-name,2-phone,3-email
                'userNickname' => 'no_required',
                'password'=>'no_required',//密码--当为type=2为phone时可以为空-直接手机短信注册，看网站需求
                'isSecurity'=>'no_required',
                'code'=>'no_required',
                'security'=>'no_required',//一个数组-密保
            ]
            , 'POST');
        if($this->verifyData['type']!=2){
            if(empty($this->verifyData['password'])){
                throw new RJsonError('请输入密码', 'PASSWORD_ERROR');
            }
        }
        if($this->verifyData['type']==2){
            if(empty($this->verifyData['code'])){
                throw new RJsonError('验证码', 'CODE_ERROR');
            }
        }
        if(!empty($this->verifyData['isSecurity']) && $this->verifyData['isSecurity']==1){
            if(empty($this->verifyData['security'])){
                throw new RJsonError('请输入您要设计的密保', 'CODE_ERROR');
            }
        }
        UserLogic::addRegister($this->verifyData);
    }

    //获取问题
    public function getSecurity()
    {
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $res = UserSecurityLogic::getSecurity($data);
        return ['data'=>$res];
    }

    //获取单条测试
    public function getOne()
    {
        $this->verify(
            [
                'account'=>'',
            ]
            , 'GET');
        $res = UserLogic::getUsers($this->verifyData['account']);
        return ['data'=>$res];
    }


}

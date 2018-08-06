<?php

namespace App\Http\Controllers\User;

use App\Logic\Common\VerifyLogic;
use App\Logic\User\AccountLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\InfoLogic;
use \App\Logic\User\LoginLogic;

class InfoController extends Controller
{

    //登录
    public function getBaseInfo(Request $request)
    {
        return [
            'data'=>InfoLogic::getBaseInfo(LoginLogic::getLoginUid())
        ];
    }
    public function sendEmail(){
        $this->verify(
            [
                'email' => '',
                'verifyGuid' => ''
            ]
            , 'POST');
        VerifyLogic::saveAndSendEmailVerify('chanage.1', $this->verifyData['verifyGuid'], $this->verifyData['email'],[
            'subject'=>'验证码'
        ]);
    }
    public function setEmail(Request $request){
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        $uid = LoginLogic::getLoginUid();
        $ip = $request->getClientIp();
        $this->verify(
            [
                'email' => '',
                'verifyGuid' => '',
                'emailVerify' => ''
            ]
            , 'POST');
        VerifyLogic::checkEmailVerify('chanage.1', $this->verifyData['verifyGuid'], $this->verifyData['email'], $this->verifyData['emailVerify']);
        try{
            // 查询是否存在手机号码
            AccountLogic::getOneByAccount($this->verifyData['email'],['uid']);
            throw new RJsonError('邮箱已经在注册', 'EMAIL_REGISTERED');
        }catch (RJsonError $e){
            if ($e->getErrorId()!=='NOT_FIND_ACCOUNT'){
                throw $e;
            }
        }
        AccountLogic::deleteOneByUidAndType($uid, 3);
        AccountLogic::addOne([
            'uid'=>$uid,
            'account'=>$this->verifyData['email'],
            'registerIp' => $ip,
            'state'=>1,
            'type'=>3
        ]);

    }
}

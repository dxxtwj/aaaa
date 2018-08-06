<?php

namespace App\Http\Controllers\V0\Admin\Admin;

use App\Logic\V0\Admin\OurLoginLogic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class OurLoginController extends Controller
{
    /**
     * 登录
     */
    public function login()
    {
        $this->verify([
            'administratorName'=>'',
            'administratorPassword'=>''
        ],'POST');

        $data=OurLoginLogic::login($this->verifyData['administratorName'],$this->verifyData['administratorPassword']);
        return ['data'=>$data];
    }


    public function isLogin(){
        if (!OurLoginLogic::isLogin()){
            return ['data'=>[
                'isLogin'=>false
            ]];
        }
        return ['data'=>[
            'isLogin'=>true
        ]];
    }

    /*
     * 退出登陆
     */
    public function logout(){
        $data = OurLoginLogic::logout();
        return ['data'=>$data];
    }
}

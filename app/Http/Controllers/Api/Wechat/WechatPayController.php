<?php

/*
 * 这个类是专门用于其他请求的，好比如获取时间戳
 * */
namespace App\Http\Controllers\Api\Wechat;


use \App\Http\Controllers\Controller;
use App\Logic\Wechat\WechatPayLogic;

/*
 * 微信支付控制器
 */
class WechatPayController extends Controller
{
    /*
     * 微信扫码付款
     */
    public function scanCodePay() {
        $this->verify(
            [

            ]
            , 'POST');
        $res = WechatPayLogic::scanCodePay();

        return ['data'=>$res];
    }

}

<?php

namespace App\Http\Controllers\Shopping\Api\Shop;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Shop\ShopLogic;
use App\Logic\V2\Common\VerifyLogic;
use App\Model\Shopping\Code\CodeModel;
use \Illuminate\Http\Request;

class ShopController extends Controller
{

    /*
     * 前台查询商家列表
     */
    public function showShop() {

        $this->verify(
            [
                'shopId' => 'no_required',//店铺ID
            ]
            , 'GET');
        $res = ShopLogic::showShop($this->verifyData);
        return $res;
    }

    /*
     * 获取验证码
     * 功能改变，改为账号密码登录
     */
    public function codeShop() {
        $this->validate(null, [
            'phone' => 'required|integer',
        ]);

        //拿IP地址
//        $request->setTrustedProxies(array('10.10.0.0/16'));
        $code = VerifyLogic::generateVerifyCode(4, '0123456789');
        //保存验证码
        self::addCode($this->verifyData['phone'],$code,1);
        //发送短信
        $name = '笨鸟文化控股11111';//签名名字，要和配置一致,目前用笨鸟文化的，如果要弄到寿司，要去添加签名
        $a = VerifyLogic::sendSmsVerify($this->verifyData['phone'],$code,$name);
        return $a;
    }

    /*
     * 保存验证码
     * 功能改变，改为账号密码登录
     */
    public static function addCode($phone,$code,$type)
    {
        $data=[
            'code_phone'=>$phone,
            'code_code'=>$code,
            'code_type'=>$type,
            'code_time' => time()+150,
        ];

        $model = new CodeModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }

    /*
     * 商家登录操作
     */
    public function loginShop() {

        $this->verify(
            [
                'shopLoginPhone' => '',
//                'code' => '',
                'shopPassword' => '',
            ]
            , 'POST');
        $res = ShopLogic::loginShop($this->verifyData);
        return $res;
    }

    /*
     * 前台商家查询订单
     */
    public function showOrder() {
        $this->verify(
            [
                'orderId' => 'no_required',//具体哪一个订单
                'status' => '',//查询那种类型  3 已完成 2 未完成
            ]
            , 'POST');
        $res = ShopLogic::showOrder($this->verifyData);
        return $res;

    }



}

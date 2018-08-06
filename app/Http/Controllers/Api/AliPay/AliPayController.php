<?php

namespace App\Http\Controllers\Api\AliPay;

use App\Http\Controllers\Controller;
use App\Logic\AliPay\AliPayLogic;

class AliPayController extends Controller
{
    /*
     * 支付宝PC版支付
     */
   public function pcAliPay() {

       $this->verify(
           [
               'out_trade_no' => 'no_required',//商品订单号
               'product_code' => 'no_required',//销售产品码
               'total_amount' => 'no_required',//订单总金额
               'subject' => 'no_required',//订单标题
               'body' => 'no_required',//订单描述
           ]
           , 'POST');

      $aliPayLogic = new AliPayLogic();
      $res = $aliPayLogic->pcAliPay($this->verifyData);

      return $res;
   }

   /*
    * 支付宝移动端支付
    */
   public function phoneAliPay() {
       $this->verify(
           [
               'out_trade_no' => 'no_required',//商品订单号
               'product_code' => 'no_required',//销售产品码
               'total_amount' => 'no_required',//订单总金额
               'subject' => 'no_required',//订单标题
               'body' => 'no_required',//订单描述
           ]
           , 'POST');

       $aliPayLogic = new AliPayLogic();
       $res = $aliPayLogic->phoneAliPay($this->verifyData);
       return $res;
   }

   /*
    * 支付宝退款 新版本
    */
   public function refundAliPay() {

    $aliPayLogin = $refundAliPay = new AliPayLogic();
    $res = $aliPayLogin->refundAliPay();
    return $res;

   }

}


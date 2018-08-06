<?php

namespace App\Http\Controllers\Shopping\Api\Order;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Order\OrderLogic;

class OrderController extends Controller
{
    /*
     * 前台添加订单
     */
    public function addOrder() {
        $this->verify(
            [
                'cartId' => '',
                'shopId' => '',//店铺ID
                'shopName' => '',//店铺名字
                'userPhone' => '',//用户手机号码
                'orderContents' => 'no_required',//订单描述
                'userDiscountId' => 'no_required', //优惠券ID
            ]
            , 'POST');
        $res = OrderLogic::addOrder($this->verifyData);
        return $res;
    }

    /*
     * 前台查询订单表
     */
    public function showOrder() {
        $this->verify(
            [
                'shopId' => '',//店铺ID
                'orderId' => 'no_required',//订单ID
                'status' => 'no_required',//订单状态
            ]
            , 'POST');
        $res = OrderLogic::showOrder($this->verifyData);
        return $res;
    }

    /*
     * 取消订单
     */
    public function deleteOrder() {

        $this->verify(
            [
                'orderId' => '',//订单ID
            ]
            , 'POST');
        $res = OrderLogic::deleteOrder($this->verifyData['orderId']);
        return $res;
    }

    /*
     * 前台订单状态更改  这里是改为3
     */
    public function editOrder() {
        $this->verify(
            [
                'orderName' => '',//订单id
//                'status' => '',//1： 待付款  2： 已付款  3：已完成 4：申请退款
            ]
            , 'POST');
        OrderLogic::editOrder($this->verifyData);
        return ;
    }

    /**
     * @param \Request $request
     * @param $orderNumber
     * @return array
     * @throws \App\Logic\Common\Exception\PaymentPayed
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     * @throws \Throwable
     * 统一下单
     */
    public function unified(){
        $this->validate(null, [
            'orderNumber' => 'required|string',
            'orderAuthGuid'    => 'string',
            'appType' => 'required|string',
            'appName' => 'required|string',
            'paymentType'    => 'integer',
            'returnUrl'=>'string'
        ]);
        $orderLogic = new OrderLogic($this->verifyData);
        $data = $orderLogic->unified($this->verifyData);
        return ['data'=>$data];
    }

}

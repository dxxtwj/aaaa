<?php

namespace App\Http\Controllers\Shangrui\Api\Order;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Order\OrderLogic;
use App\Model\V1\Order\OrderGoodsModel;

class OrderController extends Controller
{
    /*
     * 添加订单
     */
    public function addOrder(){
        $this->verify(
            [
                'cartId' => '',
                'userPhone' => '',
                'orderContents' => '', //订单描述
                'addressId' => 'no_required', //地址ID
            ]
            ,'POST');
        OrderLogic::addOrder($this->verifyData);
        return;
    }
    /*
     * 修改订单
     */
    public function editOrder(){
        $this->verify(
            [
                'orderName' => '',
            ]
            ,'POST');
        OrderLogic::editOrder($this->verifyData);
        return;
    }
    /*
     * 查询订单
     */
    public function showOrder(){
        $this->verify(
            [
                'orderId' => 'no_required',
                'status' => 'no_required',
            ]
            ,'GET');
        $res = OrderLogic::showOrder($this->verifyData);
        return $res;
    }
    /*
     * 删除订单
     */
    public function deleteOrder(){
        $this->verify(
            [
                'orderId' => '',
            ]
            ,'POST');
        OrderLogic::deleteOrder($this->verifyData['orderId']);
        return;
    }


}

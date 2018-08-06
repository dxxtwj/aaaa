<?php

namespace App\Http\Controllers\Shopping\Admin\Order;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Order\OrderLogic;

class OrderController extends Controller
{
    /*
     * 后台查询订单
     */
    public function showOrder() {
        $this->verify(
            [
                'orderId' => 'no_required',//订单表ID
                'orderStatus' => 'no_required',//订单状态
                'shopId' => 'no_required',//店铺ID
                'timeStart' => 'no_required',//开始时间
                'timeStop' => 'no_required',//结束时间
                'refundStatus' => 'no_required',//退款状态    1：退款中  2：退款成功  3：退款失败
            ]
            , 'GET');
        $res = OrderLogic::showOrder($this->verifyData);
        return $res;
    }

}

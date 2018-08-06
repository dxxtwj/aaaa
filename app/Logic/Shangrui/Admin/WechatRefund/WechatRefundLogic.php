<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\WechatRefund;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Order\OrderModel;

class WechatRefundLogic extends ShoppingLogic
{
    /*
     * 退款更改订单状态
     */
    public static function editOrder($data = array()){
        $orderModel = new OrderModel();
        $status['refund_status'] = 3 ;//退款失败
        $orderModel->where('order_id',$data['orderId'])->updateByHump($status);
        return ;
    }

}
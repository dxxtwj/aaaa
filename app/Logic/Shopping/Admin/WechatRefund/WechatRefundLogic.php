<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\WechatRefund;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Order\OrderModel;

class WechatRefundLogic extends ShoppingLogic
{
    /*
     * 退款更改订单状态
     */
    public static function editOrder($data) {

        $orderModel = new OrderModel();
        $status['refund_status'] = 3;//退款失败
        $status['shop_contents'] = empty($data['contents']) ? '' :$data['contents'];//拒接退款原因
        $orderModel->where('order_id',$data['orderId'])->updateByHump($status);
        return ;
    }


}
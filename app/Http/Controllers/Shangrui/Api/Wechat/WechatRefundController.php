<?php

namespace App\Http\Controllers\Shangrui\Api\Wechat;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Admin\WechatRefund\WechatRefundLogic;
use App\Model\Shangrui\Order\OrderModel;

class WechatRefundController extends Controller
{

    /*
     * 申请退款
     * @param int status 灵活传 0: 无操作  1：退款中  2：退款成功 3 退款失败
     * @return ;
     */
    public function editRefundStatus() {
        $this->verify(
            [
                'orderId' => '',//订单ID
                'contents' => 'no_required',//退款理由
                'explain' => 'no_required',//退款补充说明
            ]
            , 'POST');
        $updata['refund_status'] = 1;//申请退款
        $updata['refund_contents'] = empty($this->verifyData['contents']) ? '' : $this->verifyData['contents'];
        $updata['order_explain'] = empty($this->verifyData['explain']) ? '' : $this->verifyData['explain'];
        $orderModel = new OrderModel();
        $orderModel->where('order_id', $this->verifyData['orderId'])->updateByHump($updata);
    }
}

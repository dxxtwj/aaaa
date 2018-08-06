<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\Order;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Order\OrderModel;
use App\Model\Shangrui\OrderGoods\OrderGoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;
use PhpParser\Node\Expr\Empty_;

class OrderLogic extends ShoppingLogic
{
    public static function showOrder($data = array()){
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();

        date_default_timezone_set('PRC');
        if (empty($data['orderId'])){ //查所有
            if (!empty($data['orderStatus'])) {
                $orderWhere['order_status'] = $data['orderStatus'];
                $orderModel = $orderModel->where($orderWhere);

            }

            if (!empty($data['timeStart']) && !empty($data['timeStop'])) {//查这个时间区间的
                $array = array($data['timeStart'], $data['timeStop']);
                $orderModel = $orderModel->whereBetween('created_at', $array);

            }

            if (!empty($data['refundStatus'])) {
                $orderModel = $orderModel->where('refund_status', $data['refundStatus']);
            }

            $orderData = $orderModel
                ->orderBy('created_at', 'DESC')
                ->getDdvPageHumpArray();
            if (!empty($orderData['lists'])) {
                foreach ($orderData['lists'] as $k => $v) {
                    $orderData['lists'][$k]['orderGoods'] = $orderGoodsModel->where('order_id',$v['orderId'])->getHumpArray();
                    if ($v['refundStatus'] != 0) {//有售后的操作
                        $orderData['lists'][$k]['AfterSale']['refundStatus'] = $v['refundStatus'];
                        $orderData['lists'][$k]['AfterSale']['refundContents'] = $v['refundContents'];
                        $orderData['lists'][$k]['AfterSale']['shopContents'] = $v['shopContents'];
                        $orderData['lists'][$k]['AfterSale']['refundExplain'] = $v['refundExplain'];
                    }

                }
            }
            return $orderData;
        } elseif (!empty($data['orderId'])){//查单条
            $orderData = $orderModel->where('order_id',$data['orderId'])->firstHumpArray();
            if (!empty($orderData)) {

                $orderGoodsData = $orderGoodsModel->where('order_id', $orderData['orderId'])->getHumpArray();
                $orderData['orderGoods'] = empty($orderGoodsData) ? array() : $orderGoodsData;

                if ($orderData['refundStatus'] != 0) {//有售后
                    $orderData['AfterSale']['refundStatus'] = $orderData['refundStatus'];
                    $orderData['AfterSale']['refundContents'] = $orderData['refundContents'];
                    $orderData['AfterSale']['refundExplain'] = $orderData['refundExplain'];
                    $orderData['AfterSale']['shopContents'] = $orderData['shopContents'];

                }
            }
            return ['data' => $orderData];
        }
    }
}
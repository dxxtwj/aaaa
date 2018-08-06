<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\Order;

use App\Logic\Common\ShoppingLogic;
use App\Logic\Exception;
use App\Model\Shangrui\Cart\CartModel;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Order\OrderModel;
use App\Model\Shangrui\OrderGoods\OrderGoodsModel;
use App\Model\Shangrui\User\UserModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class  OrderLogic
{
    // 创建订单
    public static function addOrder($data=array()){
        $orderModel = new OrderModel();
        $goodsModel = new GoodsModel();
        $cartModel = new CartModel();
        $price = array();

        if (!empty($data['cartId'])){
            $cart = $cartModel->where('cart_id',$data['cartId'])->getHumpArray();
            if (!empty($cart)){
                foreach ($cart as $k=>$v){
                    $goods = $goodsModel->where('goods_id', $v['goodsId'])->firstHumpArray();
                    if ($goods['goodsStatus'] == 0){
                        $cartModel->where('cart_id',$v['cartId'])->delete();
                        throw new RJsonError('该商品已下架，请重新下单','ORDER_ERROR');
                    }
                    $price[] = (float)$goods['goodsPrice'] * (float)$v['cartNumber'];//单个商品的价格  数量*单价
                }
                $orderData['user_id'] = \Session::get('userId');
                $orderData['user_phone'] = $data['userPhone'];
                $orderData['order_name'] = substr(str_shuffle(time() . mt_rand(10000000, 9999999999)), 0, 15);//订单号
                $orderData['order_status'] = 1;
                $orderData['order_total_price'] = array_sum($price);
                $orderData['order_contents'] = empty($data['orderContents']) ? '' : $data['orderContents'];
                $orderData['address_id'] = empty($data['addressId']) ? 0 : $data['addressId'];

                $orderModel->setDataByArray($orderData)->save();
                $lastId = $orderModel->getQueueableId();
                if (!$lastId){
                    throw new RJsonError('下单失败','ORDER_ERROR');
                }
                foreach ($cart as $kk => $vv){
                    $goodsData = $goodsModel->where('goods_id',$vv['goodsId'])->firstHumpArray();
                    $orderGoodsModel = new OrderGoodsModel();
                    $orderGoodsData['order_id'] = $lastId;//订单id
                    $orderGoodsData['goods_id'] = $goodsData['goodsId'];//订单商品表的数据
                    $orderGoodsData['goods_img'] = $goodsData['goodsImg'];//订单商品表的数据
                    $orderGoodsData['goods_price'] = $goodsData['goodsPrice'];//订单商品表的数据
                    $orderGoodsData['goods_name'] = $goodsData['goodsName'];//订单商品表的数据
                    $orderGoodsData['goods_number'] = $vv['cartNumber'];//订单商品表的数据

                    $bool = $orderGoodsModel
                        ->setDataByHumpArray($orderGoodsData)
                        ->save();//添加进订单商品表
                    if (!$bool){
                        throw new RJsonError('下单失败','ORDER_ERROR');
                    }
                }
            }
        }
    }

    // 修改订单
    public static function editOrder($data=array()){
        $orderModel = new OrderModel();
        $order = $orderModel->where('order_name',$data['orderName'])->firstHumpArray();
        if (empty($order)){
            throw new RJsonError('暂无该订单信息','ORDER_ERROR');
        }
        if ($order['orderStatus'] == 1) {// 未付款
            throw new RJsonError('该订单还未付款', 'ORDER_ERROR');
        }
        if ($order['orderStatus'] == 3) {// 已付款
            throw new RJsonError('该订单已完成', 'ORDER_ERROR');
        }

        if ($order['orderStatus'] == 2) {// 已付款
            if ($order['refundStatus'] == 1) {
                throw new RJsonError('该订单有售后申请操作', 'ORDER_ERROR');
            }
            if ($order['refundStatus'] == 2) {
                throw new RJsonError('该订单已经退款了', 'ORDER_ERROR');
            }
            $status['order_status'] = 3;//1： 待付款  2： 已付款  3：已完成
            $bool = $orderModel->where('order_name', $data['orderName'])->updateByHump($status);

            if (!$bool) {
                throw new RJsonError('操作失败', 'ORDER_ERROR');
            }
        }

        return;
    }

    // 订单查询
    public static function showOrder($data = array()){
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        if (empty($data['orderId'])){
            $orderWhere['user_id'] = \Session::get('userId');
            $orderWhere['order_delete'] = 1;
            if (!empty($data['status'])){
                $orderWhere['order_status'] = $data['status'];
            }
            $orderData = $orderModel
                ->where($orderWhere)
                ->orderBy('order_id','DESC')
                ->getDdvPageHumpArray();
            foreach ($orderData['lists'] as $k => $v){
                $orderData['lists'][$k]['orderGoodsData'] = $orderGoodsModel
                    ->where('order_id',$v['orderId'])
                    ->getHumpArray();
                if ($v['refundStatus'] != 0) {
                    $orderData['lists'][$k]['AfterSale']['refundStatus'] = $v['refundStatus'];
                    $orderData['lists'][$k]['AfterSale']['refundContents'] = $v['refundContents'];
                    $orderData['lists'][$k]['AfterSale']['shopContents'] = $v['shopContents'];
                    $orderData['lists'][$k]['AfterSale']['refundExplain'] = $v['refundExplain'];

                }
            }
            return $orderData;

        } elseif (!empty($data['orderId'])){
            $orderData = $orderModel->where('order_id',$data['orderId'])->firstHumpArray();
            $orderData['orderGoodsData'] = $orderGoodsModel->where('order_id',$data['orderId'])->getHumpArray();
            if ($orderData['refundStatus'] != 0) {//有售后

                $orderData['AfterSale']['refundStatus'] = $orderData['refundStatus'];
                $orderData['AfterSale']['refundContents'] = $orderData['refundContents'];
                $orderData['AfterSale']['refundExplain'] = $orderData['refundExplain'];
                $orderData['AfterSale']['shopContents'] = $orderData['shopContents'];
            }
            return ['data' => $orderData];
        }
    }

    // 删除订单
    public static function deleteOrder($orderId){

        $orderModel = new OrderModel();
        $update['order_delete'] = 2;
        $where['order_id'] = $orderId;
        $bool = $orderModel->where($where)->updateByHump($update);
        if (!$bool){
            throw new RJsonError('删除订单失败','ORDER_ERROR');
        }
        return ;
    }
    
}

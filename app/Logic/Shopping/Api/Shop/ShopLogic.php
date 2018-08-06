<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Shop;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Order\OrderModel;
use App\Model\Shopping\OrderGoods\OrderGoodsModel;
use App\Model\Shopping\Shop\ShopModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use \Illuminate\Http\Request;

class ShopLogic extends ShoppingLogic
{

    /*
     * 前台查询商家列表
     */
    public static function showShop($data= array()) {

        $shopModel = new ShopModel();
        $bool = false;

        if (empty($data)) {

            $res = $shopModel->orderBy('shop_order', 'DESC')->getDdvPageHumpArray();
            if (!empty($res)) {
                foreach ($res['lists'] as $k => $v) {

                    $res['lists'][$k]['center']['lng'] = $v['lng'];
                    $res['lists'][$k]['center']['lat'] = $v['lat'];
                    unset($res['lists'][$k]['lng']);
                    unset($res['lists'][$k]['lat']);
                }

            }
        } else {
            $res = $shopModel->where('shop_id', $data['shopId'])->firstHumpArray();

            if (!empty($res)) {

                $res['center']['lng'] = $res['lng'];
                $res['center']['lat'] = $res['lat'];
                unset($res['lng']);
                unset($res['lat']);
                $bool = true;
            }
        }
        if (empty($res)) {
            throw new RJsonError('暂无数据', 'SHOP_ERROR');
        }
        if ($bool) {
            return ['data' => $res];
        }
        return $res;
    }


    /*
     * 前台商家登录
     */
    public static function loginShop($data) {

        $shopModel = new ShopModel();
        $shopData = $shopModel
            ->where('shop_login_phone', $data['shopLoginPhone'])
            ->firstHumpArray();
        if ($data['shopPassword'] != $shopData['shopPassword'] || empty($shopData)) {
            throw new RJsonError('账号或密码错误', 'PSSWORD_ERROR');
        }

        \Session::put('shopId', $shopData['shopId']);

//        $codeModel = new CodeModel();
//        $where['code_code'] = $data['code'];
//        $where['code_phone'] = $data['phone'];
//
//        $codeFirst = $codeModel->where($where)->firstHumpArray();
//
//        if (empty($codeFirst)) {
//
//            throw new RJsonError('手机号或验证码错误','CODE_ERROR');
//        }
//
//        if (time() > $codeFirst['code_time']) {//过期了
//
//            throw new RJsonError('验证码过期了,请重新获取', 'CODE_ERROR');
//
//        } else {//没有过期,登录
//
//            $homeController = new HomeController();
//            $homeController->login('shopId',11);
//
//        }
    }
    /*
     * 商家查询订单
     * @param $bool int|bool 判断是否登录，未登录返回false
     * @param $data array orderId 订单ID  为空查所有
     * @return array
     */
    public static function showOrder($data) {

        // 判断商家是否登录
        $shopping = new ShoppingLogic();
        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        $bool = $shopping->isLogin('shopId');

        if (is_bool($bool)) {

            throw new RJsonError('商家未登录', 'SHOP_ERROR');
        }
        if (empty($data['orderId'])) {

            $where['order_status'] = $data['status'];
            $where['order_delete'] = 1;
            $where['shop_id'] = \Session::get('shopId');
            $orderData = $orderModel->where($where)->getDdvPageHumpArray();
            if ($orderData) {

                foreach ($orderData['lists'] as $k => $v) {
                    $orderData['lists'][$k]['orderGoods'] = $orderGoodsModel->where('order_id',$v['orderId'])->getHumpArray();
                }
            }

            return $orderData;

        } else {

            $where['order_id'] = $data['orderId'];
            $orderData = $orderModel->where($where)->firstHumpArray();
            $orderData['goodsData'] = empty($orderData) ? array() : $orderGoodsModel->where('order_id', $orderData['orderId'])->getHumpArray();
            return ['data' => $orderData];

        }
    }
}
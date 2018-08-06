<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Cart;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Cart\CartModel;
use App\Model\Shopping\Goods\GoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use App\Model\Shopping\Discount\DiscountModel;
use App\Model\Shopping\UserDiscount\UserDiscountModel;

class CartLogic extends ShoppingLogic
{
    /*
     * 添加购物车
     */
    public static function addCart($data) {

        $goodsModel = new GoodsModel();
        $cartModel = new CartModel();
        $goodsData =  $goodsModel->where('goods_id', $data['goodsId'])->firstHumpArray();
        if ((int)$goodsData['goodsShow'] === 0) {
            throw new RJsonError('该商品已经下架', 'CART_ERROR');
        }
        $arrayWhere['user_id'] =  \Session::get('userId');
        $arrayWhere['goods_id'] = $data['goodsId'];
        $arrayWhere['shop_id'] = $data['shopId'];
        $cartData = $cartModel->where($arrayWhere)->firstHumpArray();

        if (!empty($cartData)) {
            $number['cart_number'] = $cartData['cartNumber'] + 1;
            $bool = $cartModel->where('cart_id', $cartData['cartId'])->updateByHump($number);
        } else {
            $cartAddData['user_id'] = $arrayWhere['user_id'];
            $cartAddData['goods_id'] = $goodsData['goodsId'];
            $cartAddData['shop_id'] = $data['shopId'];
            $cartAddData['cart_number'] = 1;
            $bool = $cartModel->setDataByHumpArray($cartAddData)->save();
        }
        if (empty($bool)) {
            throw new RJsonError('添加购物车失败', 'CART_ERROR');
        }
        return ;
    }

    /*
     * 购物车的加加减减
     */
    public static function editCart($data) {

        $cartModel = new CartModel();
        $cartData = $cartModel->where('cart_id', $data['cartId'])->firstHumpArray();
        if ($data['type'] == 1) {//加
            $cartAddData['cart_number'] = $cartData['cartNumber'] + 1;
        } elseif($data['type'] == 2) {//减
            if ($cartData['cartNumber'] <= 1) {
                throw new RJsonError('不能低于1', 'CART_ERROR');
            }
            $cartAddData['cart_number'] = $cartData['cartNumber'] - 1;
        }
        $bool = $cartModel->where('cart_id', $data['cartId'])->updateByHump($cartAddData);
        if (empty($bool)) {
            throw new RJsonError('购物车修改失败 ', 'CART_ERROR');
        }
        return ;

    }
    /*
     * 购物车获取单个商品
     */
    public static function getGoods($goodsId) {

        $goodsModel = new GoodsModel();
        $where['goods_id'] = $goodsId;
        $where['goods_show'] = 1;
        $goodsData = $goodsModel->where($where)->where($where)->firstHumpArray();

        if (empty($goodsData)) {
            return false;
        }
        $goodsData['goodsSpecialPrice'] = empty($goodsData['goodsSpecialPrice']) ? array() : json_decode($goodsData['goodsSpecialPrice'],true);
        $goodsData['goodsIntroduce'] = empty($goodsData['goodsIntroduce']) ? array() : json_decode($goodsData['goodsIntroduce'], true);
        return $goodsData;
    }

    /*
     * 查询购物车
     * @param bool|array  $cartData['goodsData'] 如果找不到返回false  找到返回商品数据
     * @return array
     */
    public static function showCart($data= array()) {

        $cartModel = new CartModel();
        $goodsModel = new GoodsModel();

        $whereCart['shop_id'] = $data['shopId'];
        $whereCart['user_id'] =  \Session::get('userId');
        if (empty($data['cartId'])) {
            $cartData = $cartModel->where($whereCart)->getHumpArray();
            if (!empty($cartData)) {
                foreach ($cartData as $k => $v) {
                    $ids[] = $v['goodsId'];
                }
                $goodsRec = $goodsModel->whereIn('goods_id', $ids)->getHumpArray(['goods_show', 'goods_id']);
                foreach ($goodsRec as $k => $v) {
                    if ($v['goodsShow'] == 0) {//下架了  则删除购物车
                        $whereCart['goods_id'] = $v['goodsId'];
                        $cartModel->where($whereCart)->delete();
                        unset($whereCart['goods_id']);
                    }
                }
                $car = $cartModel
                    ->where($whereCart)
                    ->getHumpArray();
                foreach ($car as $k => $v) {
                    $car[$k]['goodsData'] = self::getGoods($v['goodsId']);
                    if ($car[$k]['goodsData'] == false) {//找不到商品或已下架
                        $cartModel->where('cart_id', $v['cartId'])->delete();
                    }
                }
                return ['lists' => $car];
            }
        } elseif (!empty($data['cartId'])) {//单条
            $cartData =  $cartModel->where('cart_id', $data['cartId'])->firstHumpArray();
            $cartData['goodsData'] = self::getGoods($cartData['goodsId']);//获取商品数据
            if ($cartData['goodsData'] == false) {
                $cartModel->where('cart_id', $data['cartId'])->delete();
                return ['data' => ''];
            } else {
                return ['data' => $cartData];
            }
        }
    }
    /*
     * 删除购物车
     */
    public static function deleteCart($data) {

        foreach ($data['cartId'] as $k => $v) {

            $cartModel = new CartModel();
            $bool = $cartModel->where('cart_id', $v)->delete();

            if (empty($bool)) {

                throw new RJsonError('删除购物车失败', 'CART_ERROR');
            }
        }

        return ;
    }
}
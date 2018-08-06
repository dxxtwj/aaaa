<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\Cart;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Cart\CartModel;
use App\Model\Shangrui\Goods\GoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

class CartLogic extends ShoppingLogic
{
    // 添加购物车
    public static function addCart($data=array()){
        $goodsModel = new GoodsModel();
        $cartModel = new CartModel();
        $bool = '';
        //商品数据信息
        $goods = $goodsModel->where('goods_id',$data['goodsId'])->firstHumpArray();
        if ((int)$goods['goodsStatus'] == 0){
            throw new RJsonError('该商品已下架','CART_ERROR');
        }
        $cartData['goods_id'] = $data['goodsId'];
        $cartData['user_id'] = \Session::get('userId');
        $cart = $cartModel->where($cartData)->firstHumpArray();
        if (!empty($cart)){
            $number['cart_number'] = $cart['cartNumber'] + 1;
            $bool = $cartModel->where('cart_id',$cart['cartId'])->updateByHump($number);
        } elseif (empty($cart)){
            $cartAddData['user_id'] = \Session::get('userId');
            $cartAddData['goods_id'] = $data['goodsId'];
            $cartAddData['cart_number'] = 1;
            $bool = $cartModel->setDataByHumpArray($cartAddData)->save();
        }
        if (!$bool){
            throw new RJsonError('添加购物车失败','CART_ERROR');
        }
        return ;
    }

    // 修改购物车 加加减减
    public static function editCart($data = array()){
        $cartModel = new CartModel();
        //查出要修改的购物车信息
        $cart = $cartModel->where('cart_id',$data['cartId'])->firstHumpArray();
        if ($data['type'] == 1){
            $edit['cart_number'] = $cart['cartNumber'] + 1;
        } elseif ($data['type'] == -1){
            if ($cart['cartNumber'] <= 1) {
                throw new RJsonError('数量不能低于1', 'CART_ERROR');
            }
            $edit['cart_number'] = $cart['cartNumber'] - 1;
        }
        $bool = $cartModel->where('cart_id',$data['cartId'])->updateByHump($edit);
        if (!$bool){
            throw new RJsonError('修改购物车失败','CART_ERROR');
        }
        return ;
    }
    // 查看单个商品信息
    public static function getGoods($goodsId){
        $goodsModel = new GoodsModel();
        $goodsWhere['goods_status'] = 1;
        $goodsWhere['goods_id'] = $goodsId;
        $goodsData = $goodsModel->where($goodsWhere)->where($goodsWhere)->firstHumpArray();
        if (empty($goodsData)){
            return false;
        }
        return $goodsData;
    }

    // 查询购物车
    public static function showCart($data = array()){
        $cartModel = new CartModel();
        $goodsModel = new GoodsModel();
        if (empty($data['cartId'])){
            // 查到此用户的所有购物车信息
            $ids = array();
            $cartWhere['user_id'] = \Session::get('userId');
            $carts = $cartModel->where($cartWhere)->getHumpArray();
            if (!empty($carts)){
                foreach ($carts as $k => $v){
                    $ids[] = $v['goodsId'];  //拿到所有商品ID
                }
                $goodsRec = $goodsModel->whereIn('goods_id',$ids)->getHumpArray(['goods_status','goods_id']);
                foreach ($goodsRec as $k => $v){
                    if ($v['goodsStatus'] == 0){ //下架了则删除购物车
                        $whereCart['goods_id'] = $v['goodsId'];
                        $cartModel->where('goods_id',$whereCart)->delete();
                        unset($whereCart['goods_id']);
                    }
                }
                $car = $cartModel->where($cartWhere)->getHumpArray();
                foreach ($car as $k=>$v){
                    $car[$k]['goodsData'] = self::getGoods($v['goodsId']);
                    if ($car[$k]['goodsData'] == false) {//找不到商品或已下架
                        $cartModel->where('cart_id', $v['cartId'])->delete();
                    }

                }
                foreach ($car as $k => $v){

                    $car[$k]['goodsData']['goodsIntroduce'] = empty($v['goodsData']['goodsIntroduce']) ? array() : json_decode($v['goodsData']['goodsIntroduce'],true);
                }

                return ['lists' => $car];

            }

        } elseif (!empty($data['cartId'])){

            $cartData = $cartModel->where('cart_id',$data['cartId'])->firstHumpArray();
            $cartData['goodsData'] = self::getGoods($cartData['goodsId']);//获取商品数据
            if (!empty($cartData)){
                $cartData['goodsData']['goodsIntroduce'] = empty($cartData['goodsData']['goodsIntroduce']) ? array() : json_decode($cartData['goodsData']['goodsIntroduce'],true);
            }
            if ($cartData['goodsData'] == false) {
                $cartModel->where('cart_id', $data['cartId'])->delete();
                return ['data' => ''];
            } else {
                return ['data' => $cartData];
            }
        }
    }

    //查询所有购物车
    public static function showAllCart($data = array()){
        $cartModel = new CartModel();
        $carts = $cartModel->whereIn('cart_id',$data['cartId'])->getHumpArray();
        foreach ($carts as $k=>$v){
            $carts[$k]['goodsData'] = self::getGoods($v['goodsId']);
            if ($carts[$k]['goodsData'] == false) {//找不到商品或已下架
                $cartModel->where('cart_id', $v['cartId'])->delete();
            }

        }
        foreach ($carts as $k => $v){

            $carts[$k]['goodsData']['goodsIntroduce'] = empty($v['goodsData']['goodsIntroduce']) ? array() : json_decode($v['goodsData']['goodsIntroduce'],true);
        }

        return ['lists' => $carts];
    }


    // 删除购物车
    public static function deleteCart($data=array()){
        $cartModel = new CartModel();
        foreach ($data['cartId'] as $k => $v){
            $bool = $cartModel->where('cart_id',$v)->delete();
            if (empty($bool)){
                throw new RJsonError('删除购物车失败','CART_ERROR');
            }
        }
        return ;
    }

}
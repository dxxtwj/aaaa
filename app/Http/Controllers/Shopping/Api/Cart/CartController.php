<?php

namespace App\Http\Controllers\Shopping\Api\Cart;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Cart\CartLogic;

class CartController extends Controller
{
    /*
     * 前台添加购物车
     */
    public function addCart() {
        $this->verify(
            [
                'shopId' => '',// 商家ID
                'goodsId' => '',// 商品id

            ]
            , 'POST');
        CartLogic::addCart($this->verifyData);
        return ;
    }

    /*
     * 购物车的加加减减
     */
    public function editCart() {

        $this->verify(
            [
                'cartId' => '',// 购物车ID
                'type' => '',// 1 是加  2 是减
            ]
            , 'POST');
        CartLogic::editCart($this->verifyData);
        return ;
    }

    /*
     * 查询购物车列表
     */
    public function showCart() {
        $this->verify(
            [
                'shopId' => '',// 店铺ID
                'cartId' => 'no_required',
            ]
            , 'GET');
        $res = CartLogic::showCart($this->verifyData);
        return $res;
    }

    /*
     * 删除购物车
     */
    public function deleteCart() {
        $this->verify(
            [
                'cartId' => '',// 购物车ID
            ]
            , 'POST');
        $res = CartLogic::deleteCart($this->verifyData);
        return $res;
    }


}

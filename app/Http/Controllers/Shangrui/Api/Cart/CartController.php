<?php

namespace App\Http\Controllers\Shangrui\Api\Cart;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Cart\CartLogic;

class CartController extends Controller
{
    /*
     * 添加购物
     */
    public function addCart(){
        $this->verify(
            [
                'goodsId' => '',
            ]
            ,'POST');
        CartLogic::addCart($this->verifyData);
        return ;
    }
    /*
     * 修改购物车 加加减减
     */
    public function editCart(){
        $this->verify(
            [
                'cartId' => '',
                'type' => '',  // 1 加 -1 减
            ],'POST');
        CartLogic::editCart($this->verifyData);
        return ;
    }
    /*
     * 查看购物车
     */
    public function showCart(){
        $this->verify(
            [
                'cartId' => 'no_required',
            ]
            ,'GET');
        $res = CartLogic::showCart($this->verifyData);
        return $res;
    }
    public function showAllCart(){
        $this->verify(
            [
                'cartId' => '',
            ]
            ,'POST');
        $res = CartLogic::showAllCart($this->verifyData);
        return $res;
    }
    /*
     * 删除购物车
     */
    public function deleteCart(){
        $this->verify(
            [
                'cartId' => '',
            ]
            ,'POST');
        $res = CartLogic::deleteCart($this->verifyData);
        return $res;
    }

}

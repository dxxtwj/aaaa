<?php

namespace App\Http\Controllers\Shopping\Api\Discount;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Discount\DiscountLogic;

class DiscountController extends Controller
{
    /*
     *  前台显示优惠券
     */
    public function showDiscount(){
        $this->verify(
            [
                'shopId' => '', //商户ID
                'fullReducedId' => 'no_required', // 优惠券ID
            ]
            ,'GET');
        $res = DiscountLogic::showDiscount($this->verifyData);
        return $res;
    }

    /*
     *  前台用户领取优惠券
     */
    public function getDiscount(){
        $this->verify(
            [
                'fullReducedId' => '', //优惠券ID
            ]
            ,'POST');
        $res = DiscountLogic::getDiscount($this->verifyData);
        return $res;
    }
    /*
     *  前台用户查看优惠券
     *  不传优惠券ID则查全部，传则查单条
     */
    public function showUserDiscount(){
        $this->verify(
            [
                'userDiscountId' => 'no_required', //用户优惠券ID
                'discountStatus' => 'no_required',  //0已使用 1 未使用 -2已过期
            ]
            ,'GET');
        $res = DiscountLogic::showUserDiscount($this->verifyData);
        return $res;
    }

}
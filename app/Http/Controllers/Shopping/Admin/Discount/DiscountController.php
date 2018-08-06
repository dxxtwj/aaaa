<?php

namespace App\Http\Controllers\Shopping\Admin\Discount;

use App\Model\Shopping\Discount\DiscountModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Discount\DiscountLogic;

class DiscountController extends Controller
{
    /*
     * 后台创建优惠券
     */
    public function addDiscount()
    {
        $this->verify(
            [
                'shopId' => '', //商家ID
                'name' => 'no_required', //优惠券标题
                'reducedMoney' => 'no_required', //满减金额
                'zheKou' => 'no_required', //折扣
                'voucherMoney' => 'no_required', // 无门槛金额
                'amountMoney' => 'no_required',  // 优惠所需金额
                'status' => '', // 是否启用 0终止  1 派发
                'start' => 'no_required', // 开始时间  默认月初
                'stop' => 'no_required', // 结束时间   默认月末
                'createdAt' => 'no_required', // 创建时间
                'discountNumber' => '', //数量
                'discountType' => '', //该优惠券的类型  1 满减  2折扣  3无门槛
                'discountAlready' => 'no_required', //已领取
                'discountUsed' => 'no_required',    // 已使用

            ]
            ,'POST');
        $res = DiscountLogic::addDiscount($this->verifyData);
        return $res;
    }

    /*
     * 后台修改优惠券
     */
    public function editDiscount()
    {
        $this->verify(
            [
                'fullReducedId' => '',  //优惠券ID
                'name' => 'no_required',  // 优惠券标题
                'reducedMoney'  => 'no_required',  //满减金额
                'zheKou'  => 'no_required', //折扣
                'voucherMoney' => 'no_required',// 代金券金额
                'amountMoney' => 'no_required', //优惠所需金额
                'status' => 'no_required',  //是否启用优惠  0 关闭  1 启用
                'discountNumber' => 'no_required', //优惠券数量
                'discountType' => 'no_required', //优惠券类型
                'start' => 'no_required',  //  开始时间  默认月初
                'stop'  => 'no_required', // 结束时间  默认月末
            ], 'POST');
        $res = DiscountLogic::editDiscount($this->verifyData);
        return $res;
    }

    /*
     * 后台查看优惠券
     * 传优惠券ID查单条 不传则查全部
     */
    public function showDiscount()
    {
        $this->verify(
            [
                'fullReducedId' => 'no_required',  //优惠券ID
                'name' => 'no_required', //优惠方式
                'shopId' => 'no_required', //商家ID
                'discountType' => 'no_required', //优惠券类型
                'stop' => 'no_required', //结束时间
                'discountAlready' => 'no_required', //已领取
            ]
            ,'GET');
        $res = DiscountLogic::showDiscount($this->verifyData);
        return $res;
    }

    /*
     * 后台删除优惠券
     */
    public function deleteDiscount()
    {
        $this->verify(
            [
                'fullReducedId' => '' //优惠券ID
            ]
            ,'POST');
        $res = DiscountLogic::deleteDiscount($this->verifyData);
        return $res;
    }

}

<?php

namespace App\Logic\Shopping\Admin\Discount;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Discount\DiscountModel;
use App\Model\Shopping\Shop\ShopModel;
use App\Model\Shopping\UserDiscount\UserDiscountModel;
use App\User;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class DiscountLogic extends ShoppingLogic
{
    /*
     * 添加优惠券
     */
    public static function addDiscount($data = array())
    {

        $discountModel = new DiscountModel();
        $shopModel = new ShopModel();
        //设置默认时区
        date_default_timezone_set('PRC');

        $common = new ShoppingLogic();
        $nian = date('Y');
        $now = time();
//        $lasttime = date(strtotime("+20day"));   //20天后的时间戳
        $time = $common->timestamp($nian, $now);

        \DB::beginTransaction();
        try{
            $discountData['shop_id'] = empty($data['shopId']) ? '0' :join(',',$data['shopId']);
            $discountData['name'] = $data['name'];
            $discountData['status'] = $data['status'];
            $discountData['start'] = empty($data['start']) ? $time[0] : $data['start'];
            $discountData['stop'] = empty($data['stop']) ? $time[1] : $data['stop'];
            $discountData['discount_type'] = $data['discountType'];
            $discountData['created_at'] = time();
            $discountData['discount_number'] =  $data['discountNumber'];

            if ($discountData['discount_type'] == 1){ //满减券

                $discountData['amount_money'] = $data['amountMoney']; // 优惠所需金额
                $discountData['reduced_money']  = $data['reducedMoney']; //满减金额
                $bool = $discountModel->setDataByArray($discountData)->save();

            } elseif ($discountData['discount_type'] == 2){ //折扣券

                $discountData['amount_money'] = $data['amountMoney']; // 优惠所需金额
                $discountData['zhe_kou'] = $data['zheKou'];
                $bool = $discountModel->setDataByArray($discountData)->save();
            } elseif ($discountData['discount_type'] == 3){ //代金券

                $discountData['voucher_money']  = $data['voucherMoney']; //满减金额
                $discountData['amount_money'] = $data['amountMoney']; // 优惠券所需金额
                $bool = $discountModel->setDataByArray($discountData)->save();
            } else {
                throw new RJsonError('添加失败，代金券类型错误', 'DISCOUNT_ERROR');
            }

            if (empty($bool)) {
                throw new RJsonError('添加失败', 'DISCOUNT_ERROR');
            }
            \DB::commit();
        }catch (QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'DISCOUNT_ERROR');
        }
    }

    /*
     * 修改优惠券
     */
    public static function editDiscount($data = array())
    {
        $discountModel = new DiscountModel();
        //设置默认时区
        date_default_timezone_set('PRC');

        $common = new ShoppingLogic();
        $nian = date('Y');
        $now = time();
        $time = $common->timestamp($nian, $now);

        $discount = $discountModel->where('full_reduced_id', $data['fullReducedId'])->firstHumpArray();
        if(!empty($discount)){
            \DB::beginTransaction();
            try {
                $discountData['name'] = empty($data['name']) ? $discount['name'] : $data['name'];
                $discountData['reduced_money'] = empty($data['reducedMoney']) ? $discount['reducedMoney'] : $data['reducedMoney'];
                $discountData['zhe_kou'] = empty($data['zheKou']) ? $discount['zheKou'] : $data['zheKou'];
                $discountData['voucher_money'] = empty($data['voucherMoney']) ? $discount['voucherMoney'] : $data['voucherMoney'];
                $discountData['amount_money'] = empty($data['amountMoney']) ? $discount['amountMoney'] : $data['amountMoney'];
                $discountData['discountNumber'] = empty($data['discountNumber']) ? $discount['discountNumber'] : $data['discountNumber'];
                $discountData['discountType'] = empty($data['discountType']) ? $discount['discountType'] : $data['discountType'];
                $discountData['status'] =  empty($data['status']) ? 0 : $data['status'];
                $discountData['start'] = empty($data['start']) ? $time[0]: $data['start'];
                $discountData['stop'] = empty($data['stop']) ? $time[1] : $data['stop'];

                $bool = $discountModel->where('full_reduced_id', $data['fullReducedId'])->updateByHump($discountData);
                if (!$bool) {

                    throw new RJsonError('修改优惠券失败', 'DISCOUNT_ERROR');
                }

                \DB::commit();
            } catch(QueryException $e) {
                \DB::rollBack();
                throw new RJsonError($e->getMessage(), 'EDITGOODS_ERROR');
            }

        }
        return ;
    }

    /*
     * 后台查询优惠券
     */
    public static function showDiscount($data = array())
    {
        $discountModel = new DiscountModel();
        $shopModel = new ShopModel();

        if (empty($data['fullReducedId'])){//  全部


            if (!empty($data['name'])){ //按优惠券标题
                $discountModel = $discountModel->where('name', 'like', '%'.$data['name'].'%');
            }

            if (!empty($data['discountType'])){ //按优惠券类型
                $discountModel = $discountModel->where('discount_type', $data['discountType']);
            }

            if (!empty($data['stop'])){ //按优惠券接数时间
                $discountModel = $discountModel->where('stop', $data['stop']);
            }

            if (!empty($data['discountAlready']) && $data['discountAlready'] != 0 ){ //查找已领取的
                $discountModel = $discountModel->where('discount_already', $data['discountAlready']);
            }

            if (!empty($data['shopId']) && $data['shopId'] != 0 ){ //按照商户ID
                $discountModel = $discountModel
                    ->whereRaw('FIND_IN_SET(?, shop_id)', $data['shopId'])
                    ->orWhere('shop_id',0);
            }

            $res = $discountModel->orderBy('shopping_full_reduced.created_at', 'DESC')->getDdvPageHumpArray();

//            foreach ($res['lists'] as $k => $v) {
//                $ids = explode(',',$v['shopId']);
//
//                $res['lists'][$k]['shopData'] = $v['shopId'] == 0
//                    ? $res['lists'][$k]['shopData'] = $shopModel->getHumpArray()
//                    :  $res['lists'][$k]['shopData'] = $shopModel->whereIn('shop_id', $ids)->getHumpArray();
//            }
//
//            foreach ($res['lists'] as $k => $v){
//                $res['lists'][$k]['reducedMoney'] = strstr($res['lists'][$k]['reducedMoney'],'.00');
//                $res['lists'][$k]['voucherMoney'] = strstr($res['lists'][$k]['voucherMoney'],'.00');
//                $res['lists'][$k]['amountMoney'] = strstr($res['lists'][$k]['amountMoney'],'.00');
//            }
//            var_dump(floatval($res['lists'][$k]['reducedMoney']));die;
//            var_dump(substr($res['lists'][0]['reducedMoney'],-3));die;
//            if($res['reducedMoney'])
            return $res;
        } elseif (!empty($data['fullReducedId'])) {// 单条
            $res = $discountModel->where('full_reduced_id', $data['fullReducedId'])->firstHumpArray();
            $ids = explode(',',$res['shopId']);

            $res['shopData'] = $shopModel->whereIn('shop_id', $ids)->getHumpArray();

            return ['data'=>$res];
        }
    }

    /*
     * 后台删除优惠券
     */

    public static function deleteDiscount($fullReducedId)
    {

        $discountModel = new DiscountModel();
        $userdiscountModel = new UserDiscountModel();
        \DB::beginTransaction();

        try{
            //查询是否有用户领取过此优惠券
            $userdiscount = $userdiscountModel->where('full_reduced_id',$fullReducedId)->firstHumpArray();

            if (!empty($userdiscount)){ //被用户领取过 就同时删除

                $discountModel->where('full_reduced_id', $fullReducedId)->delete();
                $userdiscountModel->where('full_reduced_id',$fullReducedId)->delete();
            } elseif (empty($userdiscount)){ //没有被领取过 就删除优惠券表中的数据
                $discountModel->where('full_reduced_id', $fullReducedId)->delete();

            }

            \DB::commit();
        } catch(QueryException $e) {

            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'GOODS_ERROR');
        }
        return ;
    }

}
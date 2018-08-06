<?php

namespace App\Logic\Shopping\Api\Discount;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Discount\DiscountModel;
use App\Model\Shopping\UserMessage\UserMessageModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use App\Model\Shopping\Shop\ShopModel;
use App\Model\Shopping\UserDiscount\UserDiscountModel;

class DiscountLogic extends ShoppingLogic
{
    /*
     * 前端显示优惠券
     */
    public static function showDiscount($data = array())
    {
        if (empty(\Session::get('userId'))){
            $shopModel = new ShopModel();
            $discountModel = new DiscountModel();
            $userDiscountModel = new UserDiscountModel();
            //设置默认时区
            date_default_timezone_set('PRC');

            if(!empty($data['shopId']) && empty($data['fullReducedId'])){ //全部
                $res1 = $discountModel
                    ->whereRaw('FIND_IN_SET(?, shop_id)', $data['shopId'])
                    ->where('status',1)
                    ->getHumpArray(['fullReducedId']);
                $res2 = $discountModel
                    ->where('shop_id', 0)
                    ->where('status', 1)
                    ->getHumpArray(['fullReducedId']);

                if (!empty($res1)){
                    foreach ($res1 as $k => $v){
                        $ids[] = $v['fullReducedId'];
                    }
                }

                if (!empty($res2)){
                    foreach ($res2 as $k => $v){
                        $ids[] = $v['fullReducedId'];
                    }
                }

                if (!empty($ids)) {
                    $res = $discountModel->whereIn('full_reduced_id',$ids)->getHumpArray();
                    foreach ($res as $k => $v){

                        $bool = $userDiscountModel
                            ->where('full_reduced_id',$v['fullReducedId'])
                            ->firstHumpArray();

                        if (!empty($bool)){
                            $res[$k]['getStatus'] = 0;
                        } elseif (empty($bool)){
                            $res[$k]['getStatus'] = 0;
                        }
                    }
                    return ['lists' => $res];
                }

            } elseif (!empty($data['fullReducedId'])){//单条
                $res = $discountModel
                    ->where('full_reduced_id',$data['fullReducedId'])
                    ->where('status',1)
                    ->firstHumpArray();

                if (!empty($res)){
                    $bool = $userDiscountModel
                        ->where('full_reduced_id',$data['fullReducedId'])
                        ->firstHumpArray();
                    if (!empty($bool)){
                        $res['getStatus'] = 0;
                    } elseif (empty($bool)) {
                        $res['getStatus'] = 0;
                    }
                    return ['data' => $res];
                }
            }
        } elseif (!empty(\Session::get('userId'))){
            $shopModel = new ShopModel();
            $discountModel = new DiscountModel();
            $userDiscountModel = new UserDiscountModel();
            //设置默认时区
            date_default_timezone_set('PRC');

            if(!empty($data['shopId']) && empty($data['fullReducedId'])){ //全部
                $res1 = $discountModel
                    ->whereRaw('FIND_IN_SET(?, shop_id)', $data['shopId'])
                    ->where('status',1)
                    ->getHumpArray(['fullReducedId']);
                $res2 = $discountModel
                    ->where('shop_id', 0)
                    ->where('status', 1)
                    ->getHumpArray(['fullReducedId']);

                if (!empty($res1)){
                    foreach ($res1 as $k => $v){
                        $ids[] = $v['fullReducedId'];
                    }
                }

                if (!empty($res2)){
                    foreach ($res2 as $k => $v){
                        $ids[] = $v['fullReducedId'];
                    }
                }

                if (!empty($ids)) {
                    $res = $discountModel->whereIn('full_reduced_id',$ids)->getHumpArray();
                    foreach ($res as $k => $v){

                        $bool = $userDiscountModel
                            ->where('full_reduced_id',$v['fullReducedId'])
                            ->where('user_id',\Session::get('userId'))
                            ->firstHumpArray();

                        if (!empty($bool)){
                            $res[$k]['getStatus'] = 1;  //已领取
                        } elseif (empty($bool)){
                            $res[$k]['getStatus'] = 0;  //未领取
                        }
                    }
                    return ['lists' => $res];
                }

            } elseif (!empty($data['fullReducedId'])){//单条
                $res = $discountModel
                    ->where('full_reduced_id',$data['fullReducedId'])
                    ->where('status',1)
                    ->firstHumpArray();

                if (!empty($res)){
                    $bool = $userDiscountModel
                        ->where('full_reduced_id',$data['fullReducedId'])
                        ->where('user_id',\Session::get('userId'))
                        ->firstHumpArray();
                    if (!empty($bool)){
                        $res['getStatus'] = 1;  //已领取
                    } elseif (empty($bool)) {
                        $res['getStatus'] = 0;   //未领取
                    }
                    return ['data' => $res];
                }
            }
        }

    }

    public static function getDiscount($data)
    {
        /*
         * 前台用户领取优惠券
         */
        $discountModel = new DiscountModel();
        $userdiscountModel = new UserDiscountModel();
        //查看是否有剩余
        $already = $discountModel->where('full_reduced_id',$data['fullReducedId'])->firstHumpArray();
        //判断剩余优惠券数量
        if ($already['discountNumber'] - $already['discountAlready'] > 0){

            //查询此用户已拥有的优惠券
            $quan = $userdiscountModel
                ->where('user_id', \Session::get('userId'))
                ->where('full_reduced_id',$data['fullReducedId'])
                ->firstHumpArray();
            if(!empty($quan)){//如果有此优惠券

                throw new RJsonError('已拥有此优惠券，不能重复领取','DISCOUNT_ERROR');

            } elseif (empty($quan)){ //如果没有此优惠券
                $discount = $discountModel->where('full_reduced_id',$data['fullReducedId'])->firstHumpArray();
                $quanData['stop'] = $discount['stop'];
                $quanData['full_reduced_id'] = $data['fullReducedId'];
                $quanData['user_id'] =  \Session::get('userId');
                $quanData['discount_number'] = 1;
                $quanData['discount_status'] = 1;
                $bool = $userdiscountModel->setDataByArray($quanData)->save();
                if (!empty($bool)){
                    //领取成功之后修改优惠券领取数量
                    $discountData['discount_already'] = $discount['discountAlready'] + 1;
                    $discountModel->where('full_reduced_id',$data['fullReducedId'])->updateByHump($discountData);
                } elseif (empty($bool)){
                    throw new RJsonError('领取优惠券失败','DISCOUNT_ERROR');
                }
            }
        } else {
            throw new RJsonError('领取失败，此优惠券已被领完','DISCOUNTSUM_ERROR');
        }
    }

    /*
     * 前台查询用回优惠券
     * 传优惠券id查单条  不传则查全部
     */
    public static function showUserDiscount($data = array())
    {
        $userdiscountModel = new UserDiscountModel();
        $discountModel = new DiscountModel();
        $shopModel = new ShopModel();
        if (empty($data['userDiscountId'])){

            //查询已使用的
            if ($data['discountStatus'] == 0){
                $res = $userdiscountModel->where('discount_status',$data['discountStatus'])
                    ->where('user_id',\Session::get('userId'))
                    ->orderBy('shopping_user_discount.user_discount_id','DESC')
                    ->getDdvPageHumpArray();

                foreach ($res['lists'] as $k =>$v) {
                    $res['lists'][$k]['discountData'] = $discountModel
                        ->where('full_reduced_id',$v['fullReducedId'])
                        ->firstHumpArray();
                }
            }
            //查询未使用的
            if ($data['discountStatus'] == 1){
                $now = time();
                $res = $userdiscountModel
                    ->where('discount_status',$data['discountStatus'])
                    ->where('user_id',\Session::get('userId'))
                    ->where('stop','>',$now)
                    ->orderBy('shopping_user_discount.user_discount_id','DESC')
                    ->getDdvPageHumpArray();

                foreach ($res['lists'] as $k =>$v) {
                    $res['lists'][$k]['discountData'] = $discountModel
                        ->where('full_reduced_id',$v['fullReducedId'])
                        ->firstHumpArray();
                }

            }

            //查询已过期的
            if ($data['discountStatus'] == -2){
                $now = time();
                $res = $userdiscountModel
                    ->where('user_id',\Session::get('userId'))
                    ->where('stop','<',$now)
                    ->orderBy('shopping_user_discount.user_discount_id','DESC')
                    ->getDdvPageHumpArray();

                foreach ($res['lists'] as $k =>$v) {
                    $res['lists'][$k]['discountData'] = $discountModel
                        ->where('full_reduced_id',$v['fullReducedId'])
                        ->firstHumpArray();
                }
            }

            return $res;
        } elseif (!empty($data['userDiscountId'])){
            $res = $userdiscountModel
                ->where('user_discount_id',$data['userDiscountId'])
                ->firstHumpArray();
            $res['discountData'] = $discountModel->where('full_reduced_id',$res['fullReducedId'])->firstHumpArray();
            $shopIds = explode(',',$res['discountData']['shopId']);
            $res['discountData']['shopData'] = $shopModel->whereIn('shop_id', $shopIds)->getHumpArray();
            return ['data' =>$res];
        }
    }
}

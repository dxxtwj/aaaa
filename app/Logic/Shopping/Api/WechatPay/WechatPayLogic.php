<?php
/**
 * Created by PhpStorm.
 * User: yao
 * Date: 2018/5/14
 * Time: 20:03
 */

namespace App\Logic\Shopping\Api\WechatPay;


use App\Logic\Common\Exception\PaymentPayed;

class WechatPayLogic
{
    /**
     * @param $data
     * @throws PaymentPayed
     * @throws \Throwable
     * 统一下单
     */
    public static function unified($data){
        try{
            \DB::beginTransaction();
            $res= self::unifiedRun($data);
            \DB::commit();
            return $res;
        }catch (PaymentPayed $exception){
            \DB::commit();
            throw $exception;
        }catch (\Throwable $exception) {
            \DB::rollBack();
            throw  $exception;
        }
    }
    public static function unifiedRun($data) {
        //获取订单数据

    }

}
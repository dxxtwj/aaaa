<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/15
 * Time: 下午8:24
 */

namespace App\Http\Controllers\V1\Open\Wechat;

use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use App\Logic\V3\Open\Wechat\OpenPlatform;
use App\Model\V1\Open\OpenAppWechatModel;
use App\Model\V1\Open\OpenPaymchWechatModel;
use App\Model\V1\Order\OrderModel;
use App\Model\V1\Payment\PaymentModel;
use DdvPhp\DdvException;
use EasyWeChat\Factory;
use App\Http\Controllers\Exception;
use EasyWeChat\OpenPlatform\Server\Guard;

class NotifyController extends \App\Http\Controllers\Controller
{
    public function payment($appId){

        $openAppWechatLogic = new OpenAppWechatLogic([
            'wechatAppId'=>$appId
        ]);
        $app = $openAppWechatLogic->getPaymentApp();

        $response = $app->handlePaidNotify(function ($message, $fail) {
            try{
                $this->paymetHandle($message);
                // 你的逻辑
                return true;
            }catch(DdvException $e) {
                $fail($e->getMessage());
            }catch(\Exception $e) {
                $fail($e->getMessage());
            }catch(\Error $e) {
                $fail($e->getMessage());
            }catch(\Throwable $e) {
                $fail($e->getMessage());
            }
        });
        return $response;
    }
    protected function paymetHandle($message){
        //支付成功
        $model = (new OrderModel())->where(['order_number' => $message['out_trade_no'], 'order_state' => 0])->first();
        if(empty($model)){
            throw new Exception('该订单状态异常', 'ORDER_STATE_ERROR');
        }
        $paymentModel = (new PaymentModel())->where(['order_number' => $message['out_trade_no']])->first();
        if (empty($paymentModel)){
            throw new Exception('该订单不存在', 'ORDER_NOT_FIND');
        }
        \DB::beginTransaction();
        try{
            $model->order_state = 1;
            $model->payment_at = strtotime($message['time_end']);
            $model->save();
            $paymentModel->payment_state = 1;
            $paymentModel->save();
            \DB::commit();
        }catch (Exception $e){
            \DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
            return true;
        /*
        // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
        $order = 查询订单($message['out_trade_no']);

        if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
            return ; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
        }

        ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

        if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
            // 用户是否支付成功
            if (array_get($message, 'trade_state') === 'SUCCESS') {
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 'paid';

                // 用户支付失败
            } elseif (array_get($message, 'result_code')) === 'FAIL') {
                $order->status = 'paid_fail';
            }
        } else {
            throw new Exception('通信失败，请稍后再通知我');
        }

        $order->save(); // 保存订单

        return ; // 返回处理完成
        */
    }
}
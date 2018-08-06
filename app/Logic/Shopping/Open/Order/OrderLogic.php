<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/2/6
 * Time: 下午12:24
 */

namespace App\Logic\V3\Open\Order;


use App\Logic\V3\Common\Order\OrderCommonLogic;
use App\Logic\Exception;
use App\Logic\V3\Common\Payment\PaymentAlipayCommonLogic;
use App\Logic\V3\Common\Payment\PaymentWxpayCommonLogic;
use Illuminate\Http\Request;
use DB;
use Throwable;
use Closure;

class OrderLogic extends OrderCommonLogic
{
    /**
     * @param $queryMethod
     * @param $orderNumber
     * @param Request|null $request
     * @return mixed
     * @throws Exception
     * @throws Throwable
     * 支付回调统一处理
     */
    public function paymentHandle($queryMethod, $orderNumber, Request $request = null){
        //订单号为空
        if (empty($orderNumber)){
            throw new Exception('没有找到该订单', 'ORDER_NUMBER_NOT');
        }
        //查询方法
        if (empty($queryMethod)){
            throw new Exception('没有找到该支付回调查询方法', 'QUERY_METHOD_NOT');
        }
        //请求数据为空
        if (empty($request)){
            $request = Request();
        }
        $method = 'paymentHandle'.ucfirst($queryMethod); //转换为对应的方法 ，类似于 paymentHandleWechat paymentHandleAliPay
        // 判断是否支持该下单方式
        if (!method_exists($this, $method)){
            throw new Exception('暂不支持该支付类型', 'METHOD_NOT_FIND');
        }
        $this->orderNumber = $orderNumber;

        try{
            // 开启事物
            DB::beginTransaction();

            // 取得订单模型,取的订单数据(根据订单号取得数据)
            $this->load($this->getOrderModel()->toHumpArray());

            // 使用初始订单号
            $this->orderNumber = $orderNumber;
            \Log::info('paymentHandle');
            \Log::info('queryMethod');
            \Log::info($queryMethod);
            \Log::info('orderNumber');
            \Log::info($orderNumber);
            // 对应的统一下单 (调用对应的下单方法)
            $res = $this->$method(function ($res = null) {
                // 判断是否支付成功
                $this->checkPaymentSuccess();
            }, $request);
            // 提交事物
            DB::commit();
            return $res;
        } catch (Throwable $e){
            \Log::info('订单支付回调 异常 - Throwable');
            \Log::info($e->getMessage());
            \Log::info($e->getLine());
            \Log::info($e->getFile());
            // 回滚
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * 微信回调(支付成功)
     * @param Closure $checkCallback
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Exception
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @throws \ReflectionException
     */
    protected function paymentHandleWechat(Closure $checkCallback,Request $request){
        $paymentWxpayCommonLogic = new PaymentWxpayCommonLogic();
        return $paymentWxpayCommonLogic->paymentHandle($this, $checkCallback);
    }

    /**
     * 支付宝回调
     * @param Closure $checkCallback
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws Exception
     * @throws \ReflectionException
     */
    protected function paymentHandleAlipay(Closure $checkCallback,Request $request){
        $paymentWxpayCommonLogic = new PaymentAlipayCommonLogic();
        return $paymentWxpayCommonLogic->paymentHandle($this, $checkCallback, $request);
    }

}
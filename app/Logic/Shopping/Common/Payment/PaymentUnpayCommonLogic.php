<?php
namespace App\Logic\Shopping\Common\Payment;

use App\Logic\Exception;

use App\Logic\Shopping\Common\LoadDataLogic;
use DdvPhp\DdvUtil\String\Conversion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;
use Throwable;

class PaymentUnpayCommonLogic extends LoadDataLogic
{

    /**
     * @param OrderCommonLogic $orderLogic
     * @param $authCode
     * @return bool
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function scanPay(OrderCommonLogic $orderLogic, $authCode){
        throw new PaymentNext('暂不支持');
    }
    /**
     * @param $userId
     * @param OrderCommonLogic $orderLogic
     * @param $orderModel
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function unified(OrderCommonLogic $orderLogic, $userId = null)
    {

    }

    /**
     * 监听回调
     * @param OrderCommonLogic $orderLogic
     * @param Closure $checkCallback
     * @param Request|null $request
     * @return Response
     * @throws Exception
     * @throws \ReflectionException
     */
    public function paymentHandle($orderLogic, Closure $checkCallback, Request $request)
    {

    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function getListsByOrderNumberOrigin(){

    }

    /**
     * @param OrderCommonLogic|null $orderLogic
     * @return null
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \ReflectionException
     */
    public function checkPaymentSuccess(OrderCommonLogic $orderLogic = null){


    }

    /**
     * @param Model $model
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    private function checkPaymentSuccessByModel(Model $model){

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/2/2
 * Time: 下午11:26
 */

namespace App\Http\Controllers\Shopping\Api\Open\Notify;

use App\Http\Controllers\Controller;

use App\Logic\Shopping\Api\Order\OrderLogic;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @param Request $request
     * @param $paymentType
     * @param $orderNumber
     * @return mixed
     * @throws \App\Logic\Exception
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle($queryMethod, $orderNumber,Request $request){
        $orderLogic = new OrderLogic();
        return $orderLogic->paymentHandle($queryMethod, $orderNumber, $request);
    }
}
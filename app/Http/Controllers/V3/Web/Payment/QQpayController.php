<?php
/**
 * Created by PhpStorm.
 * User: aji
 * Date: 2018/1/18
 * Time: 上午9:45
 */

namespace App\Http\Controllers\V3\Api\Web\Payment;

use App\Http\Controllers\Controller;
use App\Logic\V3\Web\Order\OrderLogic;
use App\Logic\V3\Web\Payment\Qpaylogic;
use App\Logic\V3\Web\Payment\WechatLogic;
use \Illuminate\Http\Request;
use App\Logic\V1\User\LoginLogic;

class QQpayController extends Controller
{
    /**QQ支付统一下单
     * @param Request $request
     * @param $orderNumber
     * @return array
     */
    public function unified(Request $request, $orderNumber)
    {
        $this->validate(['orderNumber' => $orderNumber], [
            'orderNumber' => 'required|string',
            'orderAuthGuid' => 'string'
        ]);
        $collectLogic = new OrderLogic();
        $collectLogic->load(array_merge(['collectUid' => LoginLogic::getLoginUid()], $this->verifyData));
        $collectData = $collectLogic->getOneByAuthCode()->toHumpArray();

        $qpayLogic = new Qpaylogic();
        $qpayLogic->load(array_merge(
            $this->verifyData,
            [
                'collectUid' => LoginLogic::getLoginUid()
            ],
            $collectData
        ));
        $result = $qpayLogic->unified();

        return [
            'data' => [
                'result'=>$result
            ]
        ];

    }
}
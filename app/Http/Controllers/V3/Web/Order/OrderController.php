<?php

namespace App\Http\Controllers\V3\Api\Web\Order;

use App\Logic\V2\Pay\ScanPayLogic;
use App\Logic\V2\User\SafeLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Logic\V2\Collect\CollectLogic;
use App\Logic\V1\User\LoginLogic;
use App\Logic\V3\Web\Order\OrderLogic;

class OrderController extends Controller
{
    /**
     * 前台订单详情
     */
    /**
     * @param Request $request
     * @param $id
     * @return array
     * @throws RJsonError
     * @throws \App\Logic\Exception
     */
    public function getInfo(Request $request, $id){
        $this->validate(['orderNumber'=>$id], [
            'orderNumber' => 'required|string',
            'orderAuthGuid'    => 'string',
        ]);
        $collectLogic = new OrderLogic();
        $collectLogic->load(array_merge(['collectUid' => LoginLogic::getLoginUid()], $this->verifyData));
        $collectData = $collectLogic->getOneByAuthCode();
        return [
            'data' => $collectData
        ];
    }

    /**
     * 生成订单
     * @return array
     * @throws RJsonError
     * @throws \App\Logic\Exception
     */
    public function store(){
        $this->validate(null, [
            'orderAmount' => ['required','regex:/^[0-9]+(.[0-9]{1,2})?$/'],
            'orderDiscountAmount' => ['required','regex:/^[0-9]+(.[0-9]{1,2})?$/'],
            'orderCollectId'    => 'required|integer',
            'remarks'       => 'string',
            'siteId'        => 'integer'
        ]);
        $orderLogic = new OrderLogic(array_merge([
            // 订单类型:下单
            'orderType' => 1,
        ], $this->verifyData));
        // 创建收款订单
        return ['data'=>$orderLogic->store()];
    }


}
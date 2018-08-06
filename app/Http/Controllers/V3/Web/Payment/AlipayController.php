<?php

namespace App\Http\Controllers\V3\Api\Web\Payment;

use App\Http\Controllers\Controller;
use App\Logic\V3\Open\OauthLogic;
use App\Logic\V3\Web\Order\OrderLogic;
use App\Logic\V3\Web\Payment\AlipayLogic;
use App\Logic\V3\Web\Payment\WechatLogic;
use DdvPhp\DdvException;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Logic\V1\User\LoginLogic;
use App\Http\Controllers\Exception;

class AlipayController extends Controller
{

    /**
     * 支付宝手机网站支付
     * @param Request $request
     * @param $orderNumber
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     */
    public function tradeWapPay(Request $request, $orderNumber){
        $this->validate(['orderNumber' => $orderNumber], [
            'orderNumber' => 'required|string',
            'orderAuthGuid'    => 'string'
        ]);
        $collectLogic = new OrderLogic();
        $collectLogic->load(array_merge(['collectUid' => LoginLogic::getLoginUid()], $this->verifyData));
        $collectData = $collectLogic->getOneByAuthCode()->toHumpArray();

        $aliPayLogic = new AlipayLogic();
        $aliPayLogic->load(array_merge(
            $this->verifyData,
            [
                'collectUid' => LoginLogic::getLoginUid()
            ],
            $collectData
        ));
        $result = $aliPayLogic->wapPay();
        return $result;
    }


    /**
     * 支付宝统一下单
     * @param Request $request
     * @param $orderNumber
     * @return array
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     */
    public function unifiedOrder(Request $request, $orderNumber){
        $this->validate(['orderNumber' => $orderNumber], [
            'orderNumber' => 'required|string',
            'orderAuthGuid'    => 'string'
        ]);

        $oauthLogic = new OauthLogic();
        $alipayUserData = [];
        try{
            $alipayUserData = $oauthLogic->getOauthData(['auth_base'],'alipay', 'web')['auth_base'];
            if (empty($alipayUserData['userId'])){
                throw new Exception('没有user_id','NO_USER_ID');
            }
        }catch (DdvException $e){
            throw new RJsonError('没有授权登录', 'NOT_OAUTH_LOGIN');
        }



        $collectLogic = new OrderLogic();
        $collectLogic->load(array_merge(['collectUid' => LoginLogic::getLoginUid()], $this->verifyData));
        $collectData = $collectLogic->getOneByAuthCode()->toHumpArray();

        $aliPayLogic = new AlipayLogic();
        $aliPayLogic->load(array_merge(
            $this->verifyData,
            [
                'collectUid' => LoginLogic::getLoginUid(),
                'userId'=>$alipayUserData['userId']
            ],
            $collectData
        ));
        $result = $aliPayLogic->unifiedOrder();
        return [
            'data' => $result
        ];
    }
}
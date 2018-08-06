<?php
/**
 * Created by PhpStorm.
 * User: aji
 * Date: 2018/1/16
 * Time: 下午2:49
 */

namespace App\Http\Controllers\V3\Api\Web\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Exception;
use App\Logic\V3\Open\OauthLogic;
use App\Logic\V3\Web\Order\OrderLogic;
use App\Logic\V3\Web\Payment\WechatLogic;
use DdvPhp\DdvException;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Logic\V1\User\LoginLogic;

class WechatController extends Controller
{

    /**
     * 微信支付统一下单
     * @param Request $request
     * @param $orderNumber
     * @return array
     * @throws RJsonError
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\Wechat\PayLib\WxPayException
     */
    public function mp(Request $request, $orderNumber){
        $this->validate(['orderNumber' => $orderNumber], [
            'orderNumber' => 'required|string',
            'orderAuthGuid'    => 'string'
        ]);

        $collectLogic = new OrderLogic();
        $collectLogic->load(array_merge(['collectUid' => LoginLogic::getLoginUid()], $this->verifyData));
        $collectData = $collectLogic->getOneByAuthCode()->toHumpArray();

        $oauthLogic = new OauthLogic();
        $wechatUserData = [];
        try{
            $wechatUserData = $oauthLogic->getOauthData(['snsapi_base'],'wechat', 'mp')['snsapi_base'];
            if (empty($wechatUserData['id'])){
                throw new Exception('没有openid','NO_OPENID');
            }
        }catch (DdvException $e){
            throw new RJsonError('没有授权登录', 'NOT_OAUTH_LOGIN');
        }

        $wechatLogic = new WechatLogic();
        $wechatLogic->load(array_merge(
            $this->verifyData,
            [
                'collectUid' => LoginLogic::getLoginUid(),
                'openId' => $wechatUserData['id']
            ],
            $collectData
        ));

        return [
            'data' => [
                'jsApiParameters'=>$wechatLogic->unifiedOrder()
            ]
        ];
    }

}
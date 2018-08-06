<?php
namespace App\Logic\V3\Open\QqConnect;

use DdvPhp\DdvUrl;
use DdvPhp\QQ\Connect;
use DdvPhp\DdvException\Error;
use App\Logic\Exception;
use App\Model\V3\Common\OpenAppQqCommonModel;
use App\Logic\V3\Common\QqConnect\OauthQqConnectCommonLogic;
use App\Logic\V3\Common\QqConnect\OpenQqConnectCommonLogin;

class OpenQqConnectLogin extends OpenQqConnectCommonLogin
{
    public $callback = '';

    /**
     * 获取授权地址
     * @param string $callback
     * @param array $scopes
     * @return string
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function getWebAuthorizationUrl($callback = '', array $scopes = []){
        // 把回调地址解析为数组
        $urlObj =  DdvUrl::parse($callback);
        // 如果地址参数不为空
        if (!empty($urlObj['query'])){
            // 解析参数为数组
            $urlQueryObj = DdvUrl::parseQuery($urlObj['query']);
            //
            if ($urlQueryObj){
                unset($urlQueryObj['app_id']);
                unset($urlQueryObj['scope']);
                unset($urlQueryObj['code']);
                unset($urlQueryObj['state']);
                unset($urlQueryObj['msg']);
            }
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        // 重新编译回调地址
        $callback = DdvUrl::build($urlObj);

        $this->getAppIdByCallback($callback);
        // 根据 $appId 从数据库组合config
        // $appId
        $config = (new OpenAppQqCommonModel())->getConfig($this->qqConnectAppId);
        $qc = new Connect($config);
        $url = $qc->qq_login(implode($scopes, ','), $callback, 'QqConnectWeb');

        return $url;
    }

    /**
     * 获取授权地址
     * @param string $callback
     * @param null $query
     * @param array $scopes
     * @return array
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \ReflectionException
     */
    public function handleWebAuthorizationUrl($callback = '', $query = null, $scopes=[]){
        //获取APPID
        $this->getAppIdByCallback($callback);
        $config = (new OpenAppQqCommonModel())->getConfig($this->qqConnectAppId);
        $qc = new Connect($config);
        \Log::info('qq_callback');
        $tokenStr = $qc->qq_callback($callback);
        \Log::info($tokenStr);
        $openIdObj = $qc->get_openid($tokenStr, false);
        \Log::info('$res');
        try{
            $res = $qc->get_user_info([
            'openid' => $openIdObj->openid
        ]);
        }catch (Error $e){
//            \Log::info($e->getMessage());
            throw new Exception($e->getMessage(),$e->getCode());
        }
        \Log::info('11');
//        \Log::info((array)$res2);

        $oauthQqConnectlogic = new OauthQqConnectCommonLogic();
        $oauthQqConnectlogic->load([
            'isLost' => $res['is_lost'],
            'nickName' => $res['nickname'],
            'gender' => $res['gender'],
            'province' => $res['province'],
            'city' => $res['city'],
            'year' => $res['year'],
            'figureUrl' => $res['figureurl'],
            'figureUrl1' => $res['figureurl_1'],
            'figureUrl2' => $res['figureurl_2'],
            'figureUrlQq1' => $res['figureurl_qq_1'],
            'figureUrlQq2' => $res['figureurl_qq_2'],
            'isYellowVip' => $res['is_yellow_vip'],
            'vip' => $res['vip'],
            'yellowVipLevel' => $res['yellow_vip_level'],
            'level' => $res['level'],
            'openId' => $openIdObj->openid
        ]);
        $data = [];
        $oauthQqConnectlogic->update();
        $data['get_user_info'] = $oauthQqConnectlogic->getAttributes();
        return $data;

    }

}
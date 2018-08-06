<?php
namespace App\Logic\V3\Open\Wechat;
use App\Logic\Exception;
use App\Logic\V2\Common\LoadDataLogic;
use App\Model\V1\Domain\DomainModel;
use App\Model\V1\Open\OpenAppWechatModel;
use App\Model\V1\Open\OpenPaymchWechatModel;
use App\Model\V1\Site\SiteModel;
use App\Model\V1\User\OauthWechatModel;
use DdvPhp\DdvUrl;
use EasyWeChat\Factory;


class OpenAppWechatLogic extends LoadDataLogic
{
    public $wechatAppId = '';
    public $wechatMchId = '';
    public $queryAuthCode = '';
    public $preAuthCode = '';
    public $authorizerAccessToken = '';
    public $authorizerRefreshToken = '';
    public $funcInfo = '';
    public $authCodeExpiredAt = '';
    public $authState = '';
    public $authCodeAt = '';
    public $url = '';

    /**
     * 开放平台的用户信息
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update($data = []){
        // 如果传入了数据就直接加载到逻辑层
        if (!empty($data)){
        $this->load($data);
        }
        try {
            // 试图获取这条数据
            $model = $this->getOne();
        }catch (Exception $e){
            // 不存在就新增
            $model = new OpenAppWechatModel();
        }
        // 读取数据
        $data = $this->getAttributes(null, ['', null]);
        $model->setDataByArray($data);
        $model->toUnderline();
        if(!$model->save()){
            throw new Exception('操作失败', 'SAVE_FAIL');
        }
        return true;
    }

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        $this->getAppId();
        $model = (new OpenAppWechatModel())->where(['wechat_app_id' => $this->wechatAppId])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该公众号信息', 'APP_INFO_NOT_FIND');
        }
        return $model;
    }
    /**
     * @return $model
     * @throws Exception
     */
    public function getUserOauth(){
        $this->getAppId();
        $model = (new OauthWechatModel())->where(['wechat_app_id' => $this->wechatAppId])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该公众号信息', 'APP_INFO_NOT_FIND');
        }
        return $model;
    }

    /**
     * @return $model
     * @throws Exception
     */
    public function getWechatInfo(){
        $model = (new OpenAppWechatModel())->where(['wechat_app_id' => $this->wechatAppId])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该公众号信息', 'APP_INFO_NOT_FIND');
        }
        return $model;
    }

    /**
     * @return array|string
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getJsConfigSign(){
        $this->getWechatAppIdByUrl();
        $officialAccount = $this->getOfficialAccount();
        $officialAccount->jssdk->setUrl($this->url);
        $jsConfig = $officialAccount->jssdk->buildConfig([
            'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'chooseWXPay'
        ],  false,  false, false);
        return $jsConfig;
    }

    /**
     * @param null $callback
     * @param array $scopes
     * @return string
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function getMpAuthorizationUrl($callback=null, $scopes=['snsapi_userinfo']){
        $this->url = $callback??$this->url;
        $urlObj =  DdvUrl::parse($this->url);
        if (!empty($urlObj['query'])){
            $urlQueryObj = DdvUrl::parseQuery($urlObj['query']);
            if ($urlQueryObj){
                unset($urlQueryObj['code']);
                unset($urlQueryObj['state']);
                unset($urlQueryObj['appid']);
            }
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        $this->url = DdvUrl::build($urlObj);
        $this->getWechatAppIdByUrl();
        $officialAccount = $this->getOfficialAccount();
        return $officialAccount->oauth->scopes($scopes)->redirect($this->url)->getTargetUrl();
    }

    /**
     * @param $query
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function handleWxappByCode($query){

        if (empty($query['appid'])&&empty($this->wechatAppId)){
            throw new Exception('appId不能为空','APPID_NOT_NULL');
        }
        if (empty($query['code'])){
            throw new Exception('code不能为空','CODE_NOT_NULL');
        }
        $this->getAppId($query['appid']);
        // 获取
        $miniProgram = $this->getMiniProgram();
        // 获取token
        $res = $miniProgram->auth->session($query['code']);
        \Log::info('*****');
        \Log::info($res);
        throw new Exception('调试');
        return ;
    }

    /**
     * @param $query
     * @return array
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function handleMpAuthorizationUrl($query, $scopes){
        if (empty($query['appid'])&&empty($this->wechatAppId)){
            throw new Exception('appId不能为空','APPID_NOT_NULL');
        }
        if (empty($query['code'])){
            throw new Exception('code不能为空','CODE_NOT_NULL');
        }
        $this->getAppId($query['appid']);
        // 获取
        $officialAccount = $this->getOfficialAccount();
        // 获取token
        $token = $officialAccount->oauth->getAccessToken($query['code']);
        // 获取用户信息
        $user = $officialAccount->oauth->user($token);
        // 微信返回给你的原样的全部信息
        $data = $user->getOriginal();
        // 获取openid
        $openId = $user->getId();
        $oauthWechat = new OauthWechatLogic();
        $oauthWechat->load(array_merge($user->getAttributes(),['wechatAppId' => $this->wechatAppId, 'openId' => $openId]));
        $oauthWechat->update();

        $res = [];
        $rest = $user->getAttributes();
        if (in_array('snsapi_userinfo', $scopes, true)){
            $res['snsapi_userinfo'] = $rest;
            $res['snsapi_base'] = [
                'id'=>$user->getId()
            ];
        }else{
            $res['snsapi_base'] = $rest;
        }

        return $res;
    }

    /**
     * 开放平台获取用户授权
     * @param null $callback
     * @return string
     * @throws \App\Model\Exception
     */
    public function getPreAuthorizationUrl($callback=null){
        $this->url = $callback??$this->url;
        $urlObj =  DdvUrl::parse($this->url);
        if (!empty($urlObj['query'])){
            $urlQueryObj = DdvUrl::parseQuery($urlObj['query']);
            if ($urlQueryObj){
                unset($urlQueryObj['auth_code']);
                unset($urlQueryObj['expires_in']);
            }
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        $this->url = DdvUrl::build($urlObj);
        $openPlatform = OpenPlatform::getOpenPlatform();
        $url = $openPlatform->getPreAuthorizationUrl($this->url); // 传入回调URI即可
        return $url;
    }

    /**
     * 微信开放平台获取用户信息
     * @param $query
     * @return mixed
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function handlePreAuthorizationUrl($query){

        if (empty($query['auth_code'])){
            throw new Exception('没有auth_code码', 'NOT_FIND_AUTH_CODE');
        }
        // 获取操作
        $openPlatform = OpenPlatform::getOpenPlatform();
        // 试图获取授权信息
        $res = $openPlatform->handleAuthorize($query['auth_code']);
        $res = $res['authorization_info'];
        $data = [
            'authorizerAccessToken'=> $res['authorizer_access_token'],
            // 刷新token
            'authorizerRefreshToken'=> $res['authorizer_refresh_token'],
            // 用于id
            'wechatAppId'=> $res['authorizer_appid'],
            // 授权信息
            'funcInfo'=> json_encode($res['func_info']),
            // 授权到期时间
            'authCodeExpiredAt'=> $res['expires_in'],
            // 授权时间
            'authCodeAt'=> time(),
        ];

        $this->load($data);
        $this->update();
        return $res;
    }

    /**
     * 通过站点Id获取支付app
     * @param null $siteId
     * @return \EasyWeChat\Payment\Application
     * @throws Exception
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     */
    public function getPaymentAppBySiteId($siteId = null){
        $siteModel = (new SiteModel())->where('site_id', $siteId)->firstHump();

        if (empty($siteModel)){
            throw new Exception('站点异常', 'SITE_NOT_FIND');
        }
        if (!empty($siteModel->wechatAppId)){
            $this->wechatAppId = $siteModel->wechatAppId;
        }
        if(empty($siteModel->wechatMchId)){
            if(empty($this->wechatAppId)){
                throw new Exception('该站点暂未绑定微信', 'WX_MCHID_NOT_FIND');
            }
        }else{
            $this->wechatMchId = $siteModel->wechatMchId;
        }

        return $this->getPaymentApp();
    }

    /**
     * @return \EasyWeChat\Payment\Application
     * @throws Exception
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     */
    public function getPaymentApp(){
        $mchId = $this->getMchId();
        $config = (new OpenPaymchWechatModel())->getConfig($mchId);

       /* $wechatConfig['mchId'] = $wechatKey->wechatMchId;
        $wechatConfig['key'] = $wechatKey->key;
        $wechatConfig['appId'] = $wechatKey->wechatAppId;
        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        $wechatConfig['sslcertPath'] = $certPath;
        $wechatConfig['sslkeyPath'] = $keyPath;*/




        $app = Factory::payment([
            // 必要配置
            'app_id'             => $config->wechatAppId,
            'mch_id'             => $mchId,
            'key'                => $config->key,   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => $config->certPath, // XXX: 绝对路径！！！！
            'key_path'           => $config->keyPath,      // XXX: 绝对路径！！！！
        ]);
        return $app;
    }

    /**
     * @param null $wechatMchId
     * @return mixed|null|string
     * @throws Exception
     */
    public function getMchId($wechatMchId=null){
        // 设置进去
        $this->wechatMchId = empty($wechatMchId)?$this->wechatMchId:$wechatMchId;
        if(empty($this->wechatMchId)){
            $this->wechatMchId = $this->getOne()->wechatMchId;
        }
        return $this->wechatMchId;
    }
    /**
     * @param null $wechatAppId
     * @return null|string
     * @throws Exception
     */
    public function getAppId($wechatAppId = null){
        // 设置进去
        $this->wechatAppId = empty($wechatAppId)?$this->wechatAppId:$wechatAppId;
        if(empty($this->wechatAppId)){
            throw new Exception('appId不能为空','APPID_NOT_NULL');
        }
        return $this->wechatAppId;
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
     * @throws Exception
     * @throws \App\Model\Exception
     */
    private function getMiniProgram(){
        return $this->getOpenPlatform()->miniProgram($this->wechatAppId, $this->authorizerRefreshToken);
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
     * @throws Exception
     * @throws \App\Model\Exception
     */
    private function getOfficialAccount(){
        return $this->getOpenPlatform()->officialAccount($this->wechatAppId, $this->authorizerRefreshToken);
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Application
     * @throws Exception
     * @throws \App\Model\Exception
     */
    private function getOpenPlatform(){
        $model = $this->getOne();
        $this->load($model->toHumpArray());
        if (empty($this->authorizerRefreshToken)){
            throw new Exception('该公众号秘钥已经不存在', 'RefreshToken_NOT_FIND');
        }

        $openPlatform = OpenPlatform::getOpenPlatform();

        return $openPlatform;
    }

    /**
     * @throws Exception
     */
    private function getWechatAppIdByUrl(){
        $urlArr = DdvUrl::parse($this->url);
        $domain = $urlArr['host'];
        $resDomain = (new DomainModel())->where(['domain' => $domain])->select('wechat_app_id')->first();
        if(empty($resDomain)){
            throw new Exception('没有找到该域名', 'DOMAIN_NOT_FIND');
        }
        if(empty($resDomain->wechat_app_id)){
            throw new Exception('没有找到该公众号', 'APPID_NOT_FIND');
        }
        $this->wechatAppId = $resDomain->wechat_app_id;
    }
}
?>
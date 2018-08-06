<?php
namespace App\Logic\V3\Open\Alipay;
use App\Logic\Exception;
use App\Model\V1\Domain\DomainModel;
use App\Model\V1\Open\OpenAppAlipayModel;
use App\Logic\V2\Common\LoadDataLogic;
use App\Model\V1\Site\SiteModel;
use DdvPhp\Alipay\AopSdk;
use DdvPhp\DdvUrl;
use DdvPhp\DdvUtil;

class OpenAppAlipayLogic extends LoadDataLogic
{
    const APP_TO_APP_AUTH = 'https://openauth.alipay.com/oauth2/appToAppAuth.htm';
    const PUBLIC_APP_AUTH = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';
    public $alipayAppId = '';
    public $appAuthCode = '';
    public $appAuthToken = null;
    public $appRefreshToken = '';
    public $authTokenExpiredAt = '';
    public $refreshTokenExpiredAt = '';
    public $authUid = '';
    public $authUserId = '';
    public $authState = '';
    public $authMethods = '';
    public $data = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        AopSdk::init();
    }

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        $model = (new OpenAppAlipayModel())->where([
            'alipay_app_id' => $this->alipayAppId,
        ])->firstHump();
        if(empty($model)){
            throw new Exception('没有找到该账户信息', 'APP_INFO_NOT_FIND');
        }
        return $model;
    }
    /**
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update($data = []){
        // 如果传入了数据就直接加载到逻辑层
        if (!empty($data)){
            $this->load($data);
        }
        // 试图获取这条数据
        try{
            $model = $this->getOne();
        }catch (Exception $e){
            // 不存在就新增
            $model = new OpenAppAlipayModel();
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
     * @param $query
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function handlePreAuthorizationUrl($query){
        if (empty($query['app_auth_code'])){
            throw new Exception('没有app_auth_code码', 'NOT_FIND_APP_AUTH_CODE');
        }
        $this->appAuthCode = $query['app_auth_code'];
        $res = $this->getOpenAuthToken();
        $this->appAuthToken = $res->app_auth_token;
        $this->appRefreshToken = $res->app_refresh_token;
        $this->alipayAppId = $res->auth_app_id;
        $this->authTokenExpiredAt = $res->expires_in;
        $this->refreshTokenExpiredAt = $res->re_expires_in;
        $this->authUserId = $res->user_id;
        $this->update();
        return $this->getAttributes();
    }

    /**
     * @param null $query
     * @param array $scopes
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function handleWebAuthorizationUrl($query = null, $scopes=[]){
        if (empty($query['auth_code'])){
            throw new Exception('没有auth_code码', 'NOT_FIND_AUTH_CODE');
        }
        $result = $this->getOAuthToken($query['app_id'], $query['auth_code']);
        $oauthAlipaylogic = new OauthAlipayLogic();
        $oauthAlipaylogic->load([
            'alipayUserId'=>$result->alipay_user_id,
            'userId' => $result->user_id,
            'accessToken' => $result->access_token,
            'refreshToken' => $result->refresh_token,
            'expiresIn' => $result->expires_in,
            'reExpiresIn' => $result->re_expires_in
        ]);
        $data = [];
        $oauthAlipaylogic->update();
        $data['auth_base'] = $oauthAlipaylogic->getAttributes();
        foreach($scopes as $scope){
            if ($scope === 'auth_base') continue;
            $data[$scope] = $this->requestAuthByScope($scope, $result->access_token, $oauthAlipaylogic);

        }
        return $data;

    }

    /**
     * @param $scope
     * @param $accessToken
     * @param OauthAlipayLogic $oauthAlipaylogic
     * @return mixed
     * @throws Exception
     */
    public function requestAuthByScope($scope, $accessToken, $oauthAlipaylogic) {
        $scopeMethod = DdvUtil\String\Conversion::underlineToHump('request_'.$scope);
        if (method_exists(self::class, $scopeMethod)){
            return call_user_func_array(array($this, $scopeMethod),array($accessToken, $oauthAlipaylogic));
        }else{
            throw new Exception($scopeMethod.'不存在', 'REQUEST_METHOD_NOT_FIND');
        }
    }

    /**
     * @param $accessToken
     * @param $oauthAlipaylogic
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function requestAuthUser($accessToken, $oauthAlipaylogic) {
        $request = new \AlipayUserInfoShareRequest();
        $res = $this->executeAsAuthToken($request, $accessToken);

        $data = [
            'avatar'=>$res->avatar,
            'city'=>$res->city,
            'gender'=>$res->gender,
            'isertified'=>$res->is_certified,
            'isStudentCertified'=>$res->is_student_certified,
            'nickName'=>$res->nick_name,
            'province'=>$res->province,
            'userId'=>$res->user_id,
            'userStatus'=>$res->user_status,
            'userType'=>$res->user_type,
        ];
        $oauthAlipaylogic->load($data);
        $oauthAlipaylogic->update();

        return $data;
    }

    /**
     * @param null $callback
     * @param array $scopes
     * @return string
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function getWebAuthorizationUrl($callback = null, array $scopes = []){
        // 获取一个支付宝客户端aop
        $aop = $this->getAopClient();

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
                unset($urlQueryObj['state']);
                unset($urlQueryObj['error_scope']);
                unset($urlQueryObj['auth_code']);
                unset($urlQueryObj['source']);
                unset($urlQueryObj['userOutputs']);
                unset($urlQueryObj['alipay_token']);
            }
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        // 重新编译回调地址
        $callback = DdvUrl::build($urlObj);

        // 构建一个跳转地址数组
        $urlObj =  DdvUrl::parse(self::PUBLIC_APP_AUTH);

        $this->getAppIdByCallback($callback);

        $urlQueryObj = [
            'app_id'=>$this->alipayAppId,
            'scope'=>implode(',', $scopes),
            'redirect_uri'=>$callback
        ];
        $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        $url = DdvUrl::build($urlObj);
        return $url;
    }

    /**
     * @param null $callback
     * @return string
     * @throws \DdvPhp\Alipay\Exception
     * @throws Exception
     */
    public function getPreAuthorizationUrl($callback = null){
        // 获取一个支付宝客户端aop
        $aop = $this->getAopClient();

        // 把回调地址解析为数组
        $urlObj =  DdvUrl::parse($callback);
        // 如果地址参数不为空
        if (!empty($urlObj['query'])){
            // 解析参数为数组
            $urlQueryObj = DdvUrl::parseQuery($urlObj['query']);
            //
            if ($urlQueryObj){
                unset($urlQueryObj['app_id']);
                unset($urlQueryObj['source']);
                unset($urlQueryObj['app_auth_code']);
            }
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        // 重新编译回调地址
        $callback = DdvUrl::build($urlObj);

        // 构建一个跳转地址数组
        $urlObj =  DdvUrl::parse(self::APP_TO_APP_AUTH);

        $urlQueryObj = [
            'app_id'=>$aop->appId,
            'redirect_uri'=>$callback
        ];
        $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        $url = DdvUrl::build($urlObj);
        return $url;
    }

    /**
     * @param $alipayAppId
     * @param null $authCode
     * @param null $refreshToken
     * @return \SimpleXMLElement
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function getOAuthToken($alipayAppId, $authCode = null, $refreshToken = null){
        if (empty($authCode)&&empty($refreshToken)){
            throw new Exception('预授权code和refresh_token不能同时为空', 'CODE_OR_REFRESH_TOKEN_MUST_ONE');

        }
        $request = new \AlipaySystemOauthTokenRequest ();
        if (!empty($authCode)){
            $request->setGrantType ('authorization_code');
            $request->setCode ($authCode );
        }
        if (!empty($refreshToken)){
            $request->setGrantType ('refresh_token');
            $request->setRefreshToken ($refreshToken);
        }
        $this->alipayAppId = $alipayAppId;
        $model = $this->getOne();
        if (empty($model->appAuthToken)){
            throw new Exception('没有找到授权支付宝到我们网站', 'NOT_FIND_AUTH_CODE');
        }
        $this->load($model->toHumpArray());
        $res = $this->executeAsAuthToken($request);
        return $res;

    }

    /**
     * @return \SimpleXMLElement
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function getOpenAuthToken(){
        if (empty($this->appAuthCode)&&empty($this->appRefreshToken)){
            throw new Exception('预授权code和refresh_token不能同时为空', 'CODE_OR_REFRESH_TOKEN_MUST_ONE');
        }
        $request = new \AlipayOpenAuthTokenAppRequest();
        $bizContentArray = [];
        if (!empty($this->appAuthCode)){
            $bizContentArray['grant_type'] = 'authorization_code';
            $bizContentArray['code'] = $this->appAuthCode;
        }
        if (!empty($this->appRefreshToken)){
            $bizContentArray['grant_type'] = 'refresh_token';
            $bizContentArray['refresh_token'] = $this->appRefreshToken;
        }
        $request->setBizContent(json_encode($bizContentArray, JSON_UNESCAPED_UNICODE));
        $res = $this->execute($request);
        if ($res->code!=='10000'){
            throw new Exception("{$res->msg}[{$res->code}]", 'GET_OPEN_AUTH_TOKEN_ERROR');
        }
        return $res;
    }

    /**
     * @param null $siteId
     * @return string
     * @throws Exception
     */
    public function getAlipayAppIdBySiteId($siteId = null){

        $siteModel = (new SiteModel())->where('site_id', $siteId)->firstHump();

        if (empty($siteModel)){
            throw new Exception('站点异常', 'SITE_NOT_FIND');
        }
        if (empty($siteModel->alipayAppId)){
            throw new Exception('没有绑定支付宝应用', 'ALIPAY_PAY_NOT_FIND');
        }
        $this->alipayAppId = $siteModel->alipayAppId;

        return $this->alipayAppId;
    }

    /**
     * @param $request
     * @param null $authToken
     * @return \SimpleXMLElement
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function executeAsAuthToken($request, $authToken = null){
        if (empty($this->appAuthToken)){
            $this->load($this->getOne()->toHumpArray());
        }
        return $this->execute($request, $authToken, $this->appAuthToken);
    }
    /**
     * @param $request
     * @param null $authToken
     * @param null $appInfoAuthtoken
     * @return \SimpleXMLElement
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     */
    public function execute($request, $authToken = null, $appInfoAuthtoken = null){
        $aop = $this->getAopClient();
        $result = $aop->execute($request, $authToken, $appInfoAuthtoken);
        if (empty($result)){
            throw new Exception('请求没有返回', 'NOT_RESULT');
        }
        if (!empty($result->error_response)){
            throw new Exception("{$result->error_response->sub_msg}[{$result->error_response->code}]", $result->error_response->sub_code);
        }
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        return $result->$responseNode;
    }

    /**
     * @return \DdvPhp\Alipay\AopClient
     * @throws \DdvPhp\Alipay\Exception
     */
    public function getAopClient(){
        return AopSdk::getAopClient(config('alipay.openApi'));
    }

    /**
     * @param $callback
     * @return mixed|string
     * @throws Exception
     */
    public function getAppIdByCallback($callback){
        $urlArr = DdvUrl::parse($callback);
        $domain = $urlArr['host'];
        $resDomain = (new DomainModel())->where(['domain' => $domain])->select('alipay_app_id')->first();
        if(empty($resDomain)){
            throw new Exception('没有找到该域名', 'DOMAIN_NOT_FIND');
        }
        if(empty($resDomain->alipay_app_id)){
            throw new Exception('没有找到该支付宝appid', 'APPID_NOT_FIND');
        }
        $this->alipayAppId = $resDomain->alipay_app_id;
        return $this->alipayAppId;
    }
}
?>
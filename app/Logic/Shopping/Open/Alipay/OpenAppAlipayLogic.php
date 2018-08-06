<?php
namespace App\Logic\V3\Open\Alipay;

use DdvPhp\DdvUrl;
use DdvPhp\DdvUtil;
use App\Logic\Exception;
use App\Logic\V3\Common\Alipay\OauthAlipayCommonLogic;
use App\Logic\V3\Common\Alipay\OpenAppAlipayCommonLogic;

class OpenAppAlipayLogic extends OpenAppAlipayCommonLogic
{
    // 应用授权给开放应用
    const APP_TO_APP_AUTH = 'https://openauth.alipay.com/oauth2/appToAppAuth.htm';
    // 应用授权登录
    const PUBLIC_APP_AUTH = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm';

    /**
     * 用户授权给应用-获取授权url
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
     * 用户授权给应用-回调处理
     * @param null $query
     * @param array $scopes
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function handleWebAuthorizationUrl($query = null, $scopes=[]){
        if (empty($query['auth_code'])){
            throw new Exception('没有auth_code码', 'NOT_FIND_AUTH_CODE');
        }
        $result = $this->getOAuthToken($query['app_id'], $query['auth_code']);
        $oauthAlipaylogic = new OauthAlipayCommonLogic();
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
     * @param OauthAlipayCommonLogic $oauthAlipaylogic
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
     * @throws \ReflectionException
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
     * 获取应用到开放应用的授权url
     * @param null $callback
     * @return string
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
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
     * 获取应用到开放应用的授权-回调处理
     * @param $query
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
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

}
?>
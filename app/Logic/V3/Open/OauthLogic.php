<?php
namespace App\Logic\V3\Open;
use App\Logic\Exception;
use App\Logic\V2\Common\LoadDataLogic;
use App\Logic\V3\Open\Alipay\OauthAlipayLogic;
use App\Logic\V3\Open\Alipay\OpenAppAlipayLogic;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use App\Logic\V3\Open\Wechat\OpenPlatform;
use App\Model\V1\Domain\DomainModel;
use App\Model\V1\Open\OpenAppWechatModel;
use DdvPhp\DdvAuthOtherLogin\Lib\AlipayWeb;
use DdvPhp\DdvException;
use DdvPhp\DdvUrl;
use Session;


class OauthLogic extends LoadDataLogic
{
    public $appType = '';
    public $appName = '';
    public $callback = '';
    public $query = [];
    public $sessionKey = '';

    /**
     * @param null $appName
     * @param null $appType
     * @return string
     */
    public function getSessionKey($appName = null, $appType = null){
        $this->appType = empty($appType)?$this->appType:$appType;
        $this->appName = empty($appName)?$this->appName:$appName;
        $this->sessionKey = 'oauth.data.'.$this->appName.'.'.$this->appType;
        return $this->sessionKey;
    }

    /**
     * @param null $appName
     * @param null $appType
     * @return mixed
     * @throws Exception
     */
    public function getOauthData(array $scopes, $appName = null, $appType = null){
        $this->appType = empty($appType)?$this->appType:$appType;
        $this->appName = empty($appName)?$this->appName:$appName;
        $key = $this->getSessionKey($this->appName, $this->appType);
        $data = [];
        foreach ($scopes as $scope){
            $data[$scope] = Session($key.$scope);
            if (empty($data[$scope])){
                throw new Exception('没有'.$scope.'数据', strtoupper($scope).'_NOT_DATA');
            }
        }
        return $data;
    }

    /**
     * @param array $scopes
     * @param array $data
     * @param null $appName
     * @param null $appType
     */
    public function setOauthData(array $scopes, array $data, $appName = null, $appType = null){
        $this->appType = empty($appType)?$this->appType:$appType;
        $this->appName = empty($appName)?$this->appName:$appName;
        $key = $this->getSessionKey($this->appName, $this->appType);
        foreach ($scopes as $scope){
            Session::put($key.$scope, $data[$scope]);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function handleLogin(){
        if (empty($this->query)||(!is_array($this->query))){
            $this->query = [];
        }
        if (empty($this->appType)){
            throw new Exception('应用类型必传', 'APP_TYPE_MUST_INPUT');
        }
        if (empty($this->appName)){
            throw new Exception('授权类型必传', 'AUTH_TYPE_MUST_INPUT');
        }
        if (empty($this->callback)){
            throw new Exception('回调地址必传', 'CALLBACK_MUST_INPUT');
        }
        $method = 'run'.ucfirst($this->appName).ucfirst($this->appType);
        if (!method_exists($this, $method)){
            throw new Exception('暂不支持该授权登录类型', 'METHOD_NOT_FIND');
        }
        $this->getSessionKey();

        return $this->$method();
    }

    /**
     * @return array
     * @throws Exception
     */
    private function runWechatWxapp(){
        $openAppWechatLogic = new OpenAppWechatLogic();
        $data = $openAppWechatLogic->handleWxappByCode($this->query);
        Session::put($this->sessionKey, $data);
        return [];
    }

    /**
     * @return array
     * @throws Exception
     */
    private function runWechatMp(){
        $openAppWechatLogic = new OpenAppWechatLogic();
        $scopes = empty($this->query['scope'])?['snsapi_userinfo']:$this->query['scope'];
        $scopes = is_array($scopes)?$scopes:explode(',', $scopes);

        $res = [];

        try{
            // 试图获取数据
            try{
                $this->getOauthData($scopes);
                // 已经有授权数据直接返回
                return $res;
            }catch (Exception $e){
                $data = $openAppWechatLogic->handleMpAuthorizationUrl($this->query, $scopes);
                if (in_array('snsapi_userinfo', $scopes, true)){
                    $scopes[] = 'snsapi_base';
                }
                $this->setOauthData($scopes, $data);
            }
        }catch(\Exception $e){
            $res['message'] = $e->getMessage();
        }catch(\Error $e){
            $res['message'] = $e->getMessage();
        }
        if (isset($res['message'])){
            $res['url'] = $openAppWechatLogic->getMpAuthorizationUrl($this->callback, $scopes);
            throw new Exception('没有授权['.$res['message'].']', 'NOT_OAUTH_LOGIN', '400', ['data'=>$res]);
        }
        return $res;
    }

    /**
     * @return array
     * @throws Exception
     * @throws \App\Model\Exception
     */
    private function runWechatPre(){
        $openAppWechatLogic = new OpenAppWechatLogic();
        $res = [];
        try{
            // 试图获取数据
            try{
                $this->getOauthData(['app_auth']);
                // 已经有授权数据直接返回
                return $res;
            }catch (Exception $e){
                $data = $openAppWechatLogic->handlePreAuthorizationUrl($this->query);
                $this->setOauthData(['app_auth'], ['app_auth'=>$data]);
            }
        }catch(\Exception $e){
            $res['message'] = $e->getMessage();
        }catch(\Error $e){
            $res['message'] = $e->getMessage();
        }
        if (isset($res['message'])){
            $res['url'] = $openAppWechatLogic->getPreAuthorizationUrl($this->callback); // 传入回调URI即可
            throw new Exception('没有授权['.$res['message'].']', 'NOT_OAUTH_LOGIN', '400', ['data'=>$res]);
        }
        return $res;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function runAlipayMp(){
        return $this->runAlipayWeb();
    }
    /**
     * @return array
     * @throws Exception
     */
    private function runAlipayWap(){
        return $this->runAlipayWeb();
    }

    /**
     * 换取授权访问令牌
     * @return array
     * @throws Exception
     */
    private function runAlipayWeb(){
        $this->appType='web';
        $this->getSessionKey();
        $openAppAlipayLogic = new OpenAppAlipayLogic();

        $scopes = empty($this->query['scope'])?['auth_user']:$this->query['scope'];
        $scopes = is_array($scopes)?$scopes: explode(',', $scopes);
        if (!in_array('auth_base', $scopes)){
            $scopes[]='auth_base';
        }

        $res = [];
        $res['alipayAppId'] = $openAppAlipayLogic->getAppIdByCallback($this->callback ?? $this->query['redirect_uri']); // 传入回调URI即可

        try{
            // 试图获取数据
            try{
                $this->getOauthData($scopes);
                // 已经有授权数据直接返回
                return $res;
            }catch (Exception $e){
                $data = $openAppAlipayLogic->handleWebAuthorizationUrl($this->query, $scopes);
                $this->setOauthData($scopes, $data);
            }
        }catch(\Exception $e){
            $res['message'] = $e->getMessage();
        }catch(\Error $e){
            $res['message'] = $e->getMessage();
        }
        if (isset($res['message'])){
            $res['url'] = $openAppAlipayLogic->getWebAuthorizationUrl($this->callback, $scopes); // 传入回调URI即可
            throw new Exception('没有授权['.$res['message'].']', 'NOT_OAUTH_LOGIN', '400', ['data'=>$res]);
        }
        return $res;
    }

    /**
     * 换取应用授权令牌
     * @return array
     * @throws Exception
     */
    private function runAlipayPre(){
        $openAppAlipayLogic = new OpenAppAlipayLogic();
        $res = [];

        try{
            // 试图获取数据
            try{
                $this->getOauthData(['app_auth']);
                // 已经有授权数据直接返回
                return $res;
            }catch (Exception $e){
                $data = $openAppAlipayLogic->handlePreAuthorizationUrl($this->query);
                $this->setOauthData(['app_auth'], ['app_auth'=>$data]);
            }
        }catch(\Exception $e){
            $res['message'] = $e->getMessage();
        }catch(\Error $e){
            $res['message'] = $e->getMessage();
        }
        if (isset($res['message'])){
            $res['url'] = $openAppAlipayLogic->getPreAuthorizationUrl($this->callback); // 传入回调URI即可
            throw new Exception('没有授权['.$res['message'].']', 'NOT_OAUTH_LOGIN', '400', ['data'=>$res]);
        }
        return $res;
    }

}
?>
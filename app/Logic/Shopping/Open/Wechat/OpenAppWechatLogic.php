<?php
namespace App\Logic\Shopping\Open\Wechat;

use DdvPhp\DdvUrl;
use App\Logic\Exception;
use App\Logic\V3\Common\Wechat\OpenPlatformCommonLogic;
use App\Logic\V3\Common\Wechat\OauthWechatCommonLogic;
use App\Logic\V3\Common\Wechat\OpenAppWechatCommonLogic;
use App\Model\V3\Common\DomainCommonModel;
use EasyWeChat\Kernel\Messages\Text;

class OpenAppWechatLogic extends OpenAppWechatCommonLogic
{


    /**
     * 小程序授权登录-code回调逻辑
     * @param $query
     * @throws Exception
     * @throws \ReflectionException
     */
    public function handleMiniByCode($query){

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
     * 公众平台授权登录-url获取
     * @param null $callback
     * @param array $scopes
     * @return string
     * @throws Exception
     * @throws \ReflectionException
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
     * 公众平台授权登录-回调处理
     * @param $query
     * @param $scopes
     * @return array
     * @throws Exception
     * @throws \ReflectionException
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
        $info = $user->getOriginal();
        // 获取openid
        $openId = $user->getId();
        $data['openId'] = $info['openid']??$openId??'';
        $data['nickName'] = $info['nickname']??'';
        $data['sex'] = $info['sex']??'';
        $data['city'] = $info['city']??'';
        $data['province'] = $info['province']??'';
        $data['country'] = $info['country']??'';
        $data['headimgUrl'] = $info['headimgurl']??'';
        $data['privilege'] = $info['privilege']??'';
        $data['unionId'] = $info['unionid']??'';
        $oauthWechat = new OauthWechatCommonLogic();
        $oauthWechat->load(array_merge($data,['wechatAppId' => $this->wechatAppId]));
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
     * 微信开放平台-url获取
     * @param null $callback
     * @return string
     * @throws Exception
     * @throws \ReflectionException
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
        $openPlatform = (new OpenPlatformCommonLogic())->openPlatform();
        $url = $openPlatform->getPreAuthorizationUrl($this->url); // 传入回调URI即可
        return $url;
    }

    /**
     * 回调处理
     * @param $query
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     */
    public function handlePreAuthorizationUrl($query){

        if (empty($query['auth_code'])){
            throw new Exception('没有auth_code码', 'NOT_FIND_AUTH_CODE');
        }
        // 获取操作
        $openPlatform = (new OpenPlatformCommonLogic())->openPlatform();
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
        // 更新授权信息到数据库
        $this->update();
        // 开启定时任务
        $this->addTimingTasksJob();
        return $res;
    }

    /**
     * 获取微信应用appid-通过url
     * @throws Exception
     */
    protected function getWechatAppIdByUrl(){
        $urlArr = DdvUrl::parse($this->url);
        $domain = $urlArr['host'];
        $resDomain = (new DomainCommonModel())->where(['domain' => $domain])->select('wechat_app_id')->first();
        if(empty($resDomain)){
            throw new Exception('没有找到该域名', 'DOMAIN_NOT_FIND');
        }
        if(empty($resDomain->wechat_app_id)){
            throw new Exception('没有找到该公众号', 'APPID_NOT_FIND');
        }
        $this->wechatAppId = $resDomain->wechat_app_id;
    }

    /**
     * jssdk-config 处理逻辑
     * @return array|string
     * @throws Exception
     * @throws \ReflectionException
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
     * * 接收到公众平台用户发了的信息的时候触发
     * ***** **** ****
     * 但是该方法的处理时间不能超过5秒，如果超过，请返回空字符串并且调用客服接口推送信息给客户
     * @param $wechatAppId
     * @param $message
     * @return string
     * @throws Exception
     * @throws \ReflectionException
     */
    public function officialAccountMessage($wechatAppId, $message){
        $this->getAppId($wechatAppId);
        //$officialAccount = $this->getOfficialAccount();

        $openPlatform = $this->getOpenPlatform();
        $wechatAppId = $this->wechatAppId;

        $name = '';
        $openid = '';
        \Log::info('公众平台事件');
        \Log::info($message);
        if (!empty($message['FromUserName'])){
            $openid = $message['FromUserName'];
            \Log::info($openid);
            //$user = $officialAccount->user->get($openid);
            //\Log::info(44);
            //$name = empty($user['nickname'])?'':$user['nickname'];
        }
        switch ($message['MsgType']) {
            case 'event':
                // return '收到事件消息';
                if ($wechatAppId=='wx570bc396a51b8ff8'||$wechatAppId=='wxd101a85aa106f53e'){

                    return $message['Event']."from_callback";
                }else{

                    return "欢迎{$name}您的光临";
                }
                break;
            case 'text':
                if ($wechatAppId=='wx570bc396a51b8ff8'||$wechatAppId=='wxd101a85aa106f53e'){
                    if ($message['Content']=='TESTCOMPONENT_MSG_TYPE_TEXT'){
                        return $message['Content']."_callback";
                    }else{
                        $auth_code = substr($message['Content'],strlen('QUERY_AUTH_CODE:'));
                        \Log::info('auth_code');
                        \Log::info($auth_code);
                        // 获取操作
                        // 试图获取授权信息
                        $res = $openPlatform->handleAuthorize($auth_code);

                        \Log::info('成功拿到授权，呵呵');

                        $officialAccount1 = $openPlatform->officialAccount($res['authorization_info']['authorizer_appid'], $res['authorization_info']['authorizer_refresh_token']);
                        \Log::info('呵呵-1');

                        $message = new Text($auth_code."_from_api");
                        \Log::info('呵呵-2');

                        try{
                            \Log::info('呵呵-3'.$openid);

                            $officialAccount1->customer_service->message($message)->to($openid)->send();
                            \Log::info('呵呵-4');
                        }catch (\Exception $e){
                            \Log::info($e->getMessage());
                            \Log::info($e->getTraceAsString());
                        }catch (\Error $e){
                            \Log::info($e->getMessage());
                            \Log::info($e->getTraceAsString());
                        }
                        // $officialAccount1->broadcasting->sendText($auth_code."_from_api");
                        \Log::info($res);/*
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
                            ];*/


                        return '';
                    }
                }else{

                    return "欢迎{$name}您的光临";
                }
                // return '收到文字消息';
                break;
            case 'image':
                // return '收到图片消息';
                return "欢迎{$name}您发的图片我看不懂，正在努力学习";
                break;
            case 'voice':
                // return '收到语音消息';
                return "欢迎{$name}您发的语音我听不懂，正在努力学习";
                break;
            case 'video':
                // return '收到视频消息';
                return "欢迎{$name}您发的视频我暂时看不懂，正在努力学习";
                break;
            case 'location':
                // return '收到坐标消息';
                return "欢迎{$name}您暂时对地理还不是很懂，正在努力学习";
                break;
            case 'link':
                // return '收到链接消息';
                return "欢迎{$name}您发的联系我不懂打开，正在努力学习";
                break;
            // ... 其它消息
            default:
                // return '收到其它消息';
                return '收到其它消息';
                break;
        }
    }


}
?>
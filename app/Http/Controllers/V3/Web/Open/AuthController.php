<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/16
 * Time: 下午2:49
 */

namespace App\Http\Controllers\V3\Api\Web\Open;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\Open\Wechat\EventController;
use App\Logic\V3\Open\OauthLogic;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use App\Logic\V3\Open\Wechat\OpenPlatform;
use App\Model\V3\WechatAuthorizer\WechatAuthorizerModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Http\Request;
use JiaLeo\Laravel\Wechat\WechatOrigin;

class AuthController extends Controller
{
    /**
     * 授权登录
     * @param Request $request
     * @return array
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     */
    public function stateLogin(Request $request){
        $this->validate(null, [
            'appType' => 'required|string',
            'appName' => 'required|string'
        ]);
        $OaLogic = new OauthLogic(array_merge(
            $this->verifyData,
            [
                'query'=>$request->input('query')
            ]
        ));
        return [
            'data'=>$OaLogic->stateLogin()
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     */
    public function handleLogin(Request $request){
        $query = $request->input('query');
//        \Log::info('1---------------------------');
//        \Log::info(json_encode($query, true));
//        \Log::info('2---------------------------');
        try{
            if (is_array($query)){
                @$_GET = array_merge($query, $_GET);
            }
        }catch (\Error $e){}
        $this->validate(null, [
            'appType' => 'required|string',
            'appName' => 'required|string',
            'callback' => 'required|string'
        ]);
        $OaLogic = new OauthLogic(array_merge(
            $this->verifyData,
            [
                'query'=>$query
            ]
        ));
//        \Log::info('3---------------------------');

//        \Log::info(json_encode($OaLogic->handleLogin(), true));
//        \Log::info('4---------------------------');
//        \Log::info(json_encode($this->verifyData, true));
//        \Log::info('5---------------------------');

        return [
            'data'=>$OaLogic->handleLogin()
        ];
    }

    /**
     * @return bool
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     */
    public function handlePreAuthorize(){
        $this->validate(null, [
            'authCode' => 'required|string',
        ]);
        $openPlatform = OpenPlatform::getOpenPlatform();
        $res = $openPlatform->handleAuthorize($this->verifyData['authCode']);
        $res = $res['authorization_info'];
        $data = [
            'authorizerAccessToken'=> $res['authorizer_access_token'],
            'authorizerRefreshToken'=> $res['authorizer_refresh_token'],
            'wechatAppId'=> $res['authorizer_appid'],
            'funcInfo'=> json_encode($res['func_info']),
            'authCodeExpiredAt'=> $res['expires_in'],
            'authState' => 1,
            'siteId'   => 1
        ];

        $OpenAppWechatLogic = new OpenAppWechatLogic();
        $OpenAppWechatLogic->load($data);
        $result = $OpenAppWechatLogic->update();
        return $result;
    }
    /*
     * curl 请求
     * @param string $url 地址
     * @param string $type 方式  get | post
     * @param array $array post数据
     * @return array
     */
    public function curl($url, $type='get', $array=array()) {
        $ch = curl_init();

        if ($type=='post') {

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $array);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
    /*
     * 获取授权地址
     * @param string auth_type  要授权的帐号类型：
     * @param string redirect_uri  回调URI
     * @param string biz_appid  指定授权唯一的小程序或公众号
     * @param string re_auth_code  预授权码
     * @param string component_appid  第三方平台方appid
     *
     * @return array url授权地址
     */
    public function getAuth() {
        $this->validate(null, [
            'auth_type' => 'required',
            'redirect_uri' => 'required',//
            'biz_appid' => 'no_required',
        ]);

        $auth_type = $this->verifyData['auth_type'];
        $redirect_uri = $this->verifyData['redirect_uri'];
        $biz_appid = $this->verifyData['biz_appid'];

        $eventController = new EventController();
        $pre_auth_code = $eventController->getPreAuthCode();
        $component_appid = config('wechat.open.app_id');

        if (empty($pre_auth_code)) {
            \Log::info('获取pre_auth_code失败,V3->AuthController');
            throw new RJsonError('获取pre_auth_code失败,V3->AuthController','CODE_ERROR');
        }
        $url = 'https://mp.weixin.qq.com/safe/bindcomponent?action=bindcomponent&auth_type='.$auth_type.'&no_scan=1&component_appid='.$component_appid.'&pre_auth_code='.$pre_auth_code.'&redirect_uri='.$redirect_uri.'&biz_appid='.$biz_appid.'#wechat_redirect';
        return ['data' => ['url' => $url]];
    }


    /*
     * 授权后回调URI，得到授权码 auth_code（authorization_code）
     * @param string auth_code 授权码
     * @param string expires_in 授权码过期时间
     *
     * 使用授权码换取公众号或小程序的接口调用凭据和授权信息
     * @param  string component_access_token 获取第三方平台component_access_token
     * @param  string component_appid 第三方平台appid
     * @param  string authorization_code 授权code,会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
     *
     * @return array 授权的信息
     * @param string authorizer_appid   授权方appid
     * @param string authorizer_access_token 授权方接口调用凭据
     * @param string expires_in 有效期
     * @param string authorizer_refresh_token 接口调用凭据刷新令牌
     */
    public function getAuthCode() {
        $this->validate(null, [
            'auth_code' => 'required',
            'expires_in' => 'required',
        ]);

        $wechatAuthModel = new WechatAuthorizerModel();
        $data['wechat_authorization_code'] = $this->verifyData['auth_code'];
        $data['wechat_authorization_expires_in'] = $this->verifyData['expires_in'] + time();

//        \Log::info('获取授权码 V3->AuthController');
//        \Log::info($data['wechat_authorization_code']);

        // 使用授权码换取公众号或小程序的接口调用凭据和授权信息
        $eventController = new EventController();
        $component_access_token = $eventController->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token;
        $component['component_appid'] = $eventController::APPID;//第三方平台appid
        $component['authorization_code'] = $data['wechat_authorization_code'];//授权code,会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
        $jsonComponent = json_encode($component, true);
        $wechatArray = json_decode($this->curl($url, 'post',$jsonComponent),  true);

//        \Log::info('wechatArray');
//        \Log::info($wechatArray);

        if (!empty($wechatArray['errcode'])) {
            $errorArray = $this->getError($wechatArray['errcode']);
            return ['data' => $errorArray];
        }

        //添加数据库
        $data['wechat_authorizer_appid'] = $wechatArray['authorization_info']['authorizer_appid'];
        $data['wechat_authorizer_access_token'] = $wechatArray['authorization_info']['authorizer_access_token'];
        $data['wechat_authorizer_expires_in'] = $wechatArray['authorization_info']['expires_in'];
        $data['wechat_authorizer_refresh_token'] = $wechatArray['authorization_info']['authorizer_refresh_token'];

        // 小程序名字和头像
        $array = $this->getAuthUserInfo($data['wechat_authorizer_appid']);

        $data['head_img'] = $array['authorizer_info']['head_img'];
        $data['nick_name'] =$array['authorizer_info']['nick_name'];

        $where['wechat_authorizer_appid'] = $wechatArray['authorization_info']['authorizer_appid'];
        $wechatAuthData = $wechatAuthModel->where($where)->firstHumpArray();

        if (!empty($wechatAuthData)) {
            $wechatAuthModel->where($where)->updateByHump($data);
        } else {
            $wechatAuthModel->setDataByHumpArray($data)->save();
        }

        return ['data' => $wechatArray];
    }

    /*
     * 授权成功后获取头像和名字等信息存进数据库
     * @param string $wechat_authorizer_appid 授权方的APPID
     * @return arrary 返回小程序名称和头像
     */
    public function getAuthUserInfo($wechat_authorizer_appid) {

        $eventController = new EventController();
        $component_access_token = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台ComponentAccessToken
        $url  = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
        $data = [
            'component_appid' => $eventController::APPID,
            'authorizer_appid' => $wechat_authorizer_appid,
        ];
        $dataJson = json_encode($data, true);
        $arrayDecode = json_decode($this->curl($url, 'post',$dataJson),true);
        if (!empty($arrayDecode['authorizer_info'])) {
            $arrayDecode['errcode'] = 0;
        }
        return $arrayDecode;
    }


    /*
     * 获取（刷新）授权公众号或小程序的接口调用凭据（令牌）
     * @param string authorizerAppid 授权方appid
     * @param string component_appid 第三方平台appid
     * @param string authorizer_refresh_token 授权方的刷新令牌
     *
     * @param array $array 微信返回的信息
     * @param string authorizer_access_token 授权方令牌
     * @param string expires_in 有效期，为2小时
     * @param string authorizer_refresh_token  刷新令牌
     *
     * @return string $wechatAuthorizerAccessToken 授权方令牌
     */
    public function getAuthorizerAccessToken($data) {
        // ------ @@@ 要在调试工具调试的时候需要开启这个  然后注释 $this->verifyData['authorizerAppid'] = $data['authorizerAppid'];这行代码
//        $this->validate(null, [
//            'authorizerAppid' => 'required',//授权方appid
//        ]);
        $this->verifyData['authorizerAppid'] = $data['authorizerAppid'];
        $where['wechat_authorizer_appid'] = $this->verifyData['authorizerAppid'];

        $autuModel = new WechatAuthorizerModel();
        $authData = $autuModel->where($where)->firstHumpArray();

        if (empty($authData) || $authData['wechatStatus'] == 2) {
            \Log::info('还没授权');
            throw new RJsonError('还没有授权, V3->AuthController->getAuthorizerAccessToken', 'AUTH_ERROR');
        }

        $wechatAuthorizerAccessToken = '';

        if ($authData['wechatAuthorizerExpiresIn'] > time()) {// 没过期

            $wechatAuthorizerAccessToken = $authData['wechatAuthorizerAccessToken'];

        } else {//过期了

            $eventController = new EventController();
            $data['authorizer_appid'] = $this->verifyData['authorizerAppid'];
            $data['component_appid'] = $eventController::APPID;
            $data['authorizer_refresh_token'] = $authData['wechatAuthorizerRefreshToken'];

            $component_access_token = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台ComponentAccessToken
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$component_access_token;
            $dataJson = json_encode($data, true);
            $array = json_decode($this->curl($url, 'post', $dataJson), true);

            if (!empty($array)) {
                // 修改数据库
                $updata['wechat_authorizer_access_token'] = $array['authorizer_access_token'];
                $updata['wechat_authorizer_refresh_token'] = $array['authorizer_refresh_token'];
                $updata['wechat_authorizer_expires_in'] = time() + $array['expires_in'];
                $autuModel->where($where)->updateByHump($updata);

                $wechatAuthorizerAccessToken = $array['authorizer_access_token'];
            }
        }

        return  $wechatAuthorizerAccessToken;
    }

    /*
     * 获取授权方的帐号基本信息
     * (1）公众号获取   小程序获取
     * @param string component_appid 第三方平台appid
     * @param string authorizer_appid 授权方appid
     * @return array 授权方的信息
     */
    public function getAuthInfo() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);

        $eventController = new EventController();
        $component_access_token = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台ComponentAccessToken
        $url  = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
        $data = [
            'component_appid' => $eventController::APPID,
            'authorizer_appid' => $this->verifyData['authorizerAppid'],
        ];
        $dataJson = json_encode($data, true);
        $array = json_decode($this->curl($url, 'post',$dataJson));
        return ['data' => $array];

    }

    /*
     * 获取授权方的帐号基本信息   貌似是跟公众号一样的
     * （2）小程序获取方法
     */
//    public function getSmallProgramUserInfo() {
//        $this->validate(null, [
//            'authorizerAppid' => 'required',//授权方appid
//        ]);
//        $eventController = new EventController();
//        $component_access_token = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台ComponentAccessToken
//        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
//
//        $data = [
//
//            'component_appid' => $eventController::APPID,
//            'authorizer_appid' => $this->verifyData['authorizerAppid'],
//        ];
//        $dataJson = json_encode($data, true);
//
//       $array =  json_decode($this->curl($url, 'post', $dataJson), true);
//
//       return ['data' => $array];
//    }




    /*
     * 微信第三方错误码
     */
    public function getError($errorCode) {

        switch ($errorCode) {
            case 61010:
                $array['code'] = 61010;
                $array['content'] = 'authorization_code过期->V3->Auth';
                break;
            default :
                $array['code'] = -2;
                $array['content'] = '不明觉厉的错误->V3->Auth';
                break;
        }
        return $array;
    }
}
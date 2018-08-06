<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/15
 * Time: 下午8:24
 */

namespace App\Http\Controllers\V1\Open\Wechat;

use App\Logic\V1\Open\Wechat\SmallPogramLogic;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use App\Model\V3\WechatAuthorizer\WechatAuthorizerModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use EasyWeChat\Kernel\Messages\Text;
use App\Logic\V3\Open\Wechat\OpenPlatform;
use EasyWeChat\OpenPlatform\Server\Guard;
use Illuminate\Database\QueryException;

include 'wxBizMsgCrypt/wxBizMsgCrypt.php';//包含进来

class EventController extends \App\Http\Controllers\Controller
{
    const APPID = 'wx4f3c8e39e32189b6';
    const TOKEN = '6951b723ed74ecda0bab540a00kf81f4e40c07e9b30';
    const ENCODINGAESKEY = '5034f246fd1d69c8673467d4bcaf940240c0744b34b';
    const APPSECRET = 'f842d93555a85f5bfeec96230422b362';

    /*
     * 微信推送过来的方法 授权事件接收URL
     * 这个方法主要获取component_verify_ticket，授权事件，一共四个
     * get post 微信都有推送过来的  post 需要解密才能看到
     * @param int  $data['wechat_small_program_status'] 0未上传代码、1已上传代码、审核中2、审核通过3、发布成功4、审核失败-1
     */
    public function ticket(){

//        $component_verify_ticket = \Cache::get('component_verify_ticket');
//        \Log::info('component_verify_ticket');
//        \Log::info($component_verify_ticket);

        // ----------------------------解密微信服务器信息区间--------------------------
        // -------微信定时推送,授权类型事件--------------

        $wechatAuthModel = new WechatAuthorizerModel();
        $text = file_get_contents('php://input');
        $msg_signature = empty($_GET['msg_signature']) ? '' : $_GET['msg_signature'];
        $timeStamp  = empty($_GET['timestamp']) ? '' : $_GET['timestamp'];
        $nonce = empty($_GET['nonce']) ? '' : $_GET['nonce'];
        $pc = new \WXBizMsgCrypt(self::TOKEN, self::ENCODINGAESKEY, self::APPID);
        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($text);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $msg = '';//这个是授权回来的事件，取消，更新，授权  三种
        $errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $from_xml, $msg);//$msg  为引用传递

        //解密成功
        if ($errCode == 0) {

            $array = $this->xmlToArray($msg);//转为数组

            // ------------------------存储ComponentVerifyTicket区间---------------

            if ($array['InfoType'] == 'component_verify_ticket') {

                $xml = new \DOMDocument();
                $xml->loadXML($msg);
                $array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
                $component_verify_ticket = $array_e->item(0)->nodeValue;
                \Cache::put('component_verify_ticket',$component_verify_ticket,30);//存
            }

            //-------------------------------处理授权事件区间------------------------
            $where['wechat_authorizer_appid'] = $array['AuthorizerAppid'];// sql  条件
            if ($array['InfoType'] == 'authorized') {//授权
                $authData = $wechatAuthModel->where($where)->firstHumpArray();

                $data = [
                    'wechat_authorization_code' => $array['AuthorizationCode'],
                    'wechat_authorization_expires_in' => $array['AuthorizationCodeExpiredTime'] + time(),
                    'wechat_status' => 1,//授权
                    'wechat_small_program_status' => empty($authData['wechatSmallProgramStatus']) ? 0: $authData['wechatSmallProgramStatus'],//判断代码
                ];
//                $getConfig['authorizerAppid'] = $array['AuthorizerAppid'];
//                $smallData = SmallPogramLogic::getConfig($getConfig);
//                if (!empty($smallData['lists'])) {// 已经上传了代码
//                    $data['wechat_small_program_status'] = 1;//
//                }
                if (empty($authData)) {//添加

                    $data['wechat_authorizer_appid'] = $array['AuthorizerAppid'];
                    $wechatAuthModel->setDataByHumpArray($data)->save();

                } else {//修改
                    $wechatAuthModel->where('wechat_authorizer_appid', $array['AuthorizerAppid'])->updateByHump($data);
                }
            } elseif ($array['InfoType'] == 'unauthorized') {//取消授权
                $dataUpdata['wechat_status'] = 2;//  取消授权状态
                $wechatAuthModel->where($where)->updateByHump($dataUpdata);
//                    \Log::info('取消成功');
//                    \Log::info($array['AppId']);
            } elseif ($array['InfoType'] == 'updateauthorized') {//更新授权->修改权限级时候
                $dataUpdata = [

                    'wechat_authorization_code' => $array['AuthorizationCode'],
                    'wechat_authorization_expires_in' => $array['AuthorizationCodeExpiredTime'] + time(),
                ];
                $wechatAuthModel->where($where)->updateByHump($dataUpdata);
            }

            echo "success";

        } else {
//                \Log::info($errCode);
            echo "false";
            \Log::info('获取component_verify_ticket失败'.'错误码:'.$errCode.'位置:V1->EventController');
            throw new RJsonError('获取component_verify_ticket失败'.'错误码:'.$errCode.'位置:V1->EventController','OPEN_ERROR');
        }



//        try{
//            $openPlatform = OpenPlatform::getOpenPlatform();
//            $server = $openPlatform->server;
//
//            // 处理授权成功事件，其他事件同理
//            $server->push(function ($message) {
//                // $message 为微信推送的通知内容，不同事件不同内容，详看微信官方文档
//                // 获取授权公众号 AppId： $message['AuthorizerAppid']
//                // 获取 AuthCode：$message['AuthorizationCode']
//                // 然后进行业务处理，如存数据库等...
//                \Log::info('EVENT_AUTHORIZED');
//                \Log::info($message);
//                $authLogic = new OpenAppWechatLogic([
//                    'wechatAppId'=> $message['AuthorizerAppid'],
//                    'queryAuthCode'=> $message['AuthorizationCode'],
//                    'preAuthCode'=> $message['PreAuthCode'],
//                    'authCodeAt'=> $message['CreateTime'],
//                    'authCodeExpiredAt'=> $message['AuthorizationCodeExpiredTime'],
//                    'authState' => 1
//                ]);
//                $authLogic->update();
//            }, Guard::EVENT_AUTHORIZED);
//
//            // 处理授权更新事件
//            $server->push(function ($message) {
//                // ...
//                \Log::info('EVENT_UPDATE_AUTHORIZED');
//                \Log::info($message);
//                $authLogic = new OpenAppWechatLogic([
//                    'wechatAppId'=> $message['AuthorizerAppid'],
//                    'queryAuthCode'=> $message['AuthorizationCode'],
//                    'preAuthCode'=> $message['PreAuthCode'],
//                    'authCodeAt'=> isset($message['CreateTime'])?$message['CreateTime']:time(),
//                    'authCodeExpiredAt'=> $message['AuthorizationCodeExpiredTime'],
//                    'authState' => 1
//                ]);
//                $authLogic->update();
//            }, Guard::EVENT_UPDATE_AUTHORIZED);
//
//            // 处理授权取消事件
//            $server->push(function ($message) {
//                // ...
//                \Log::info('EVENT_UNAUTHORIZED');
//                \Log::info($message);
//                $authLogic = new OpenAppWechatLogic([
//                    'wechatAppId'=> $message['AuthorizerAppid'],
//                    'authCodeExpiredAt'=> time(),
//                    'authState' => 0
//                ]);
//                $authLogic->update();
//            }, Guard::EVENT_UNAUTHORIZED);
//            return $server->serve();
//        }catch (\Exception $e){
//
//            \Log::info('一个错误');
//            \Log::info($e->getMessage());
//        }catch (\Error $e){
//
//            \Log::info('一个错误');
//            \Log::info($e->getMessage());
//        }
    }

    /*
     * 获取第三方平台component_access_token
     * @param string component_appid 第三方平台appid
     * @param string component_appsecret 第三方平台appsecret
     * @param string component_verify_ticket 微信后台推送的ticket，此ticket会定时推送
     * @return array component_access_token 第三方平台component_access_token
     */
    public function getComponentAccessToken() {

        $component_access_token = \Cache::get('component_access_token');

        if (empty($component_access_token)) {

            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
            $component_verify_ticket = \Cache::get('component_verify_ticket');
            $param = [

                'component_appid' => self::APPID,
                'component_appsecret' => self::APPSECRET,
                'component_verify_ticket' => $component_verify_ticket,
            ];
            $jsonParam = json_encode($param);
//            \Log::info('param');
//            \Log::info($jsonParam);
            $array = json_decode($this->curl($url, 'post', $jsonParam), true);
            if (empty($array['component_access_token'])) {
                \Log::info('component_access_token获取失败,可能是component_verify_ticket没获取到,V1->EventController->getComponentAccessToken');

                throw new RJsonError('component_access_token获取失败,可能是component_verify_ticket没获取到,V1->EventController->getComponentAccessToken','OPEN_ERROR');
            }
            //存缓存
            \Cache::put('component_access_token', $array['component_access_token'], 110);
            $component_access_token = $array['component_access_token'];

        }
        return ['component_access_token' => $component_access_token];
    }


    /*
     * 获取预授权码pre_auth_code
     * @param string component_appid 第三方平台方appid
     * @return string 预授权码pre_auth_code
     */
    public function getPreAuthCode() {
        $component_access_token = $this->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$component_access_token;

        $param = [
            'component_appid' => self::APPID,
        ];
        $jsonParam = json_encode($param,true);
        $pre_auth_code = $this->curl($url, 'post', $jsonParam);
        $arrayPreAuthCode = json_decode($pre_auth_code,true);
        if (empty($arrayPreAuthCode['pre_auth_code'])) {
            \Log::info('pre_auth_code请求失败，位置:V1->EventController');
            throw new RJsonError('pre_auth_code请求失败，位置:V1->EventController', 'OPEN_ERROR');
        }

        return $arrayPreAuthCode['pre_auth_code'];
    }

    /*
     * CURL
     * @param string $url 地址
     * @param string $type post|get 必须小写
     * @param array post数据
     */
    public function curl($url, $type='get', $data=array()) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//存储变量

        if ($type == 'post') {

            curl_setopt($ch,CURLOPT_POST,1);// 表明是post
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//post数据
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);////禁用证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//禁用证书验证

        $array = curl_exec($ch);//执行cURL
        curl_close($ch);//关闭cURL资源，并且释放系统资源

        return $array;
    }


    /*
     * 数组转换为XML格式
     * @param array 要转换的数组
     * @return xml 转换后的数据
     */
    public  function arrayToXml($arr) {
        if(!is_array($arr) || count($arr) == 0) return '';
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /*
     * XML格式转换为数组
     * @param xml 要转换的xml
     * @return array 转换后的数据
     */
    public  function xmlToArray($xml) {
        if($xml == '') return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /*
     * 消息与事件接收URL 这个方法会回调小程序的审核情况，公众号的信息等
     */
    public function callback($wechatAppId){


        // 判断审核时间回调开始
        $text = file_get_contents('php://input');
        $msg_signature = empty($_GET['msg_signature']) ? '' : $_GET['msg_signature'];
        $timeStamp  = empty($_GET['timestamp']) ? '' : $_GET['timestamp'];
        $nonce = empty($_GET['nonce']) ? '' : $_GET['nonce'];
        $pc = new \WXBizMsgCrypt(self::TOKEN, self::ENCODINGAESKEY, self::APPID);
        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($text);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $msg = '';
        $errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $from_xml, $msg);//$msg  为引用传递

        if ($errCode == 0) {
            $array = $this->xmlToArray($msg);//转为数组、
//            \Log::info('转为数组的回调数据，即审核结果   callback->解密');
//            \Log::info($array);
//            \Log::info($wechatAppId);
//            \Log::info('转为数组的回调数据，即审核结果   callback->解密结束');
            $wechatAuth = new WechatAuthorizerModel();

            if ($array['Event'] == 'weapp_audit_success') {//通过

                $wechatAuthUpdata['wechat_small_program_status'] = 4;//审核通过
                $wechatAuthUpdata['wechat_small_program_fail_time'] = $array['SuccTime'];
                $wechatAuthUpdata['wechat_small_program_reason'] = '';
                $wechatAuth->where('wechat_authorizer_appid', $wechatAppId)->updateByHump($wechatAuthUpdata);

            } elseif ($array['Event'] == 'weapp_audit_fail') {//失败

                $wechatAuthUpdata['wechat_small_program_status'] = -1;// 审核失败
                $wechatAuthUpdata['wechat_small_program_reason'] = $array['Reason'];
                $wechatAuthUpdata['wechat_small_program_fail_time'] = $array['FailTime'];
                $wechatAuth->where('wechat_authorizer_appid', $wechatAppId)->updateByHump($wechatAuthUpdata);
            }
        }
        // 判断审核时间回调结束

        $openPlatform = OpenPlatform::getOpenPlatform();
        $officialAccount = $openPlatform->officialAccount($wechatAppId);
        $server = $officialAccount->server; // ❗️❗️  这里的 server 为授权方的 server，而不是开放平台的 server，请注意！！！
        $server->push(function ($message) use ($wechatAppId) {
            $openPlatform = OpenPlatform::getOpenPlatform();
            $officialAccount = $openPlatform->officialAccount($wechatAppId);
            $name = '';
            $openid = '';
            if (!empty($message['FromUserName'])){
                $openid = $message['FromUserName'];
//                \Log::info('公众平台事件');
//                \Log::info($message);
//                \Log::info($openid);
               //$user = $officialAccount->user->get($openid);
//                \Log::info(44);
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
//                            \Log::info('auth_code');
//                            \Log::info($auth_code);
                            // 获取操作
                            // 试图获取授权信息
                            $res = $openPlatform->handleAuthorize($auth_code);

//                            \Log::info('成功拿到授权');

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
                    return '您发的图片我看不懂，正在努力学习';
                    break;
                case 'voice':
                    // return '收到语音消息';
                    return '您发的语音我听不懂，正在努力学习';
                    break;
                case 'video':
                    // return '收到视频消息';
                    return '您发的视频我暂时看不懂，正在努力学习';
                    break;
                case 'location':
                    // return '收到坐标消息';
                    return '我暂时对地理还不是很懂，正在努力学习';
                    break;
                case 'link':
                    // return '收到链接消息';
                    return '您发的联系我不懂打开，正在努力学习';
                    break;
                // ... 其它消息
                default:
                    // return '收到其它消息';
                    return '收到其它消息';
                    break;
            }

        });

        return $server->serve();


    }
}
<?php

namespace App\Http\Controllers\Shopping\Api\Wechat;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\WechatLogin\WechatLoginLogic;

class WechatController extends Controller
{

    const APPID = 'wx3d1656447b0c8f6a';// appid
    const APPSECRET = 'e371443f0c000921d773b7b1dab8591d';

    //开启session
    public function __construct() {

        session_start();//原生session
        date_default_timezone_set('PRC');

    }

    /*
     * 获取微信分享接口参数
     */
    public function shareWechat() {
        $this->verify(
            [
                'url' => ''//当前网页的URL
            ]
            , 'GET');
        $url = $this->verifyData['url'];
        //$url='http://bnwh.cdn.easyke.top/web/lists/vote/1/';
        $jsapiTicket = $this->getJsApiTicket();
        /* // 注意 URL 一定要动态获取，不能 hardcode.（获取当前网页的url）*/
        /*$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";*/
        //时间戳
        $timestamp = time();
        //随机字符串获取
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
//        $arr = [
//            'jsapi_ticket' => $jsapiTicket,
//            'noncestr' => $nonceStr,
//            'timestamp' => $timestamp,
//            'url' => $url,
//        ];
//        ksort($arr);
//        $string = http_build_query($arr);
//        //生成字符串是用来签名用的
        $signature = sha1($string);
        $signPackage = array(
            "appId"   => self::APPID,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "signature" => $signature,
            "rawString" => $string,
            "url"    => $url,
        );
        return ['data'=>$signPackage];
    }
    /*
     * 获取随机数
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /*
     * 获取jsApiTicket
     */
    public function getJsApiTicket() {
        if (!empty($_SESSION['JsApiTicketTime']) && $_SESSION['JsApiTicketTime'] > time()) {
            $jsApiTicket =  $_SESSION['jsApiTicket'];
        } else {

            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $jsApiTicket = $res->ticket;
            $_SESSION['jsApiTicket'] = $jsApiTicket;
            $_SESSION['JsApiTicketTime'] = time() + 6500;
        }
        return $jsApiTicket;
    }
    /*
     * 获取accesstonken
     */
    public function getAccessToken(){

        if (!empty($_SESSION['accessTokenTime']) && $_SESSION['accessTokenTime'] > time()) {
            $access_token = $_SESSION['accessToken'];

        } else {

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".self::APPID."&secret=".self::APPSECRET;
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;

            $_SESSION['accessToken'] = $access_token;
            $_SESSION['accessTokenTime'] = time() + 6500;
        }

        return $access_token;
    }

    /*
     * get  CURL
     */
    private function httpGet($url) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//存储变量
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);//设置一个长整形数，作为最大延续多少秒。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);////禁用证书验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//禁用证书验证
        curl_setopt($curl, CURLOPT_URL, $url);//地址
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;

    }

    /*
     * 创建客服
     * @param string kfAccount 完整客服帐号，格式为：帐号前缀@公众号微信号，帐号前缀最多10个字符，必须是英文、数字字符或者下划线，后缀为公众号微信号，长度不超过30个字符
     * @param string nickName 客服昵称，最长16个字
     * @reuturn array
     */
    public function addCustomerService(){
        $this->verify(
            [
                'kfAccount' => '',
                'nickName' => '',
            ]
            , 'POST');
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$this->getAccessToken().'';
        $arr = [
            'kf_account' => $this->verifyData['kfAccount'],
            'nickname' => $this->verifyData['nickName'],
        ];
        $data = json_encode($arr,true);//转JSON发送

        $text = self::http_curl($url,'post','json',$data);
        $returnArray = self::getSwitch($text['errcode']);
        return ['data' =>$returnArray];
    }

    /*
     * 获取客服信息
     */
    public function getCusetomerService() {

        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.$this->getAccessToken().'';
        $text = self::http_curl($url,'get');

        return ['data' =>$text];
    }

    /*
     * 获取当前客服在线之类的接口
     */
    public function getCusetomerServiceCurrent() {

        $url2 = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$this->getAccessToken();
        $text = self::http_curl($url2,'get');

        return ['data' => $text];
    }

    /*
     * 邀请绑定客服账号
     * @param string kfAccount 完整客服帐号
     * @param string inviteWx 接收绑定邀请的客服微信号
     * @return array
     */
    public function InvitationCusetomerService () {
        $this->verify(
            [
                'kfAccount' => '',
                'inviteWx' => '',//接收绑定邀请的客服微信号
            ]
            , 'POST');

        $arr = [

            'kf_account' => $this->verifyData['kfAccount'],
            'invite_wx' => $this->verifyData['inviteWx'],
        ];

        $jsonText = json_encode($arr,true);
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token='.$this->getAccessToken();
        $text = self::http_curl($url, 'post','json',$jsonText);
        $array = self::getSwitch($text['errcode']);
        return ['data' => $array];

    }

    /*
     * 创建会话
     * @param string kfAccount
     * @param string openid
     * @retrun array
     */
    public function addConversation() {
        $this->verify(
            [
                'kfAccount' => '',
                'openid' => '',//接收绑定邀请的客服微信号
            ]
            , 'POST');

        $url = 'https://api.weixin.qq.com/customservice/kfsession/create?access_token='.$this->getAccessToken();

        $arr = [
            'kf_account' => $this->verifyData['kfAccount'],
            'openid' => $this->verifyData['openid'],
        ];
        $arrJson = json_encode($arr, true);
        $text =  self::http_curl($url,'post','json',$arrJson);
        $array = self::getSwitch($text['errcode']);
        return ['data'=>$array];
    }

    /*
     * 获取错误信息
     * @param string $code
     * return array
     */
    public static function getSwitch($code){

        switch ($code) {
            case 0:
                $array['errcode'] = '0';
                $array['errmsg'] = '邀请已发送';
                break;
            case 65400:
                $array['errcode'] = '65400';
                $array['errmsg'] = 'API不可用，即没有开通/升级到新版客服';
                break;
            case 65401:
                $array['errcode'] = '65401';
                $array['errmsg'] = '无效客服帐号';
                break;
            case 65402:
                $array['errcode'] = '65402';
                $array['errmsg'] = '帐号尚未绑定微信号，不能投入使用';
                break;
            case 65403:
                $array['errcode'] = '65403';
                $array['msg'] = '客服昵称不合法';
                break;
            case 65404:
                $array['errcode'] = '65404';
                $array['msg'] = '客服帐号不合法';
                break;
            case 65405:
                $array['errcode'] = '65405';
                $array['msg'] = '帐号数目已达到上限，不能继续添加';
                break;
            case 65406:
                $array['errcode'] = '65406';
                $array['msg'] = '已经存在的客服帐号';
                break;
            case 65407:
                $array['errcode'] = '65407';
                $array['errmsg'] = '邀请对象已经是本公众号客服';
                break;
            case 65408:
                $array['errcode'] = '65408';
                $array['errmsg'] = '本公众号已发送邀请给该微信号';
                break;
            case 65409:
                $array['errcode'] = '65409';
                $array['errmsg'] = '无效的微信号';
                break;
            case 65410:
                $array['errcode'] = '65410';
                $array['errmsg'] = '邀请对象绑定公众号客服数量达到上限（目前每个微信号最多可以绑定5个公众号客服帐号）';
                break;
            case 65411:
                $array['errcode'] = '65411';
                $array['errmsg'] = '该帐号已经有一个等待确认的邀请，不能重复邀请';
                break;
            case 65412:
                $array['errcode'] = '65412';
                $array['errmsg'] = '该帐号已经绑定微信号，不能进行邀请';
                break;
            case 65413:
                $array['errcode'] = '65413';
                $array['errmsg'] = '不存在对应用户的会话信息';
                break;
            case 65414:
                $array['errcode'] = '65414';
                $array['errmsg'] = '客户正在被其他客服接待';
                break;
            case 40003:
                $array['errcode'] = '40003';
                $array['errmsg'] = '非法的openid';
                break;
            default: $array = array();
                break;
        }
    }

    /**
     * $url 接口url string
     * $type 请求类型 string
     * $res 返回数据类型 string
     * $arr post 请求参数 string
     */
    public static function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        //1.初始化curl
        $ch = curl_init();
        //2.设置curl参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//不直接输出，以变量的方式存储起来
        if($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        } elseif ($type == 'get') {
        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        //请求成功
        return json_decode($output,true);

    }

}

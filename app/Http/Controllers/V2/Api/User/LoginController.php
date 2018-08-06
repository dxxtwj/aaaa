<?php

namespace App\Http\Controllers\V2\Api\User;

use App\Logic\V2\Common\VerifyLogic;
use App\Logic\User\AccountLogic;
use App\Logic\User\RegisterLogic;
use App\Logic\User\UserPhoneRegisteringLogic;
use App\Logic\V2\Kujiale\KujialeLogic;
use App\Logic\V2\User\UserLogic;
use App\Model\UserPhoneRegisteringModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use \App\Logic\User\InfoLogic;
use \App\Logic\V2\User\LoginLogic;

class LoginController extends Controller
{
    //登录
    public function login()
    {
        $this->validate(null, [
            'account' => 'required',
            'type'=>'required',
            'password' => 'required|string',
            //'code' => ''
        ]);
        LoginLogic::login($this->verifyData);
        return;
    }

    //登录状态
    public function isLogin(){
        if (!LoginLogic::isLogin()){
           /* throw new RJsonError('没有登录', 'NO_LOGIN');*/
            return ['data'=>[
                'isLogin'=>false
            ]];
        }
        return ['data'=>[
            'isLogin'=>true
        ]];
    }

    //退出登录
    public function logout()
    {
        LoginLogic::logout();
        return;
    }

    //检测这个账号是否存在
    public function AccountDetect()
    {
        $this->verify(
            [
                'account' => '',
                'type' => '',
            ]
            , 'POST');
        UserLogic::checkAccount($this->verifyData);
        return;
    }

    //笨鸟-微信
    public function auth()
    {
        $this->verify(
            [
                'type' => '',
            ]
            , 'GET');
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8956bb1b3181f683&redirect_uri=http://api.shangrui.cc/test.html&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';

    }

    /*
     * 酷家乐登录
     */
    public function loginKuJiaLe() {


        $this->verify(
            [
                'name' => '',
                'password' => '',
                'new' => 'no_required',
                'dest'=>''
            ]
            , 'POST');
        $res = KujialeLogic::loginKuJiaLe($this->verifyData);
        $appkey='qOpB5l0k70';
        $appsecret='IcAPNjgmmiEjGGgLWdwsh1Ton8zpscns';
        $dest = $this->verifyData['dest'];
        //账号
        $timestamp=$this->get_subtraction();
        $appuid=$res['keyId'];
        $sign = md5($appsecret.$appkey.$appuid.$timestamp);
        $url='http://www.kujiale.com/p/openapi/login?';
        $asd = [
            'appuid' => $appuid,
            'appuname' => $res['name'],
            'appuemail' => '',
            'appuphone' => '',
            'appussn' => '',
            'appuaddr' => '',
            'appuavatar' => '',
            'appkey' => $appkey,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'dest' => $dest,
            'apputype' => 0,
            //'designid' => '',
        ];
        $o = "";
        foreach ( $asd as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $asd = substr($o,0,-1);

        $data = json_decode($this->request_post($url,$asd));
        return $data;
    }

    public function kujialePassword()
    {
        $this->verify(
            [
                'name' => '',
                'passwordOld' => '',
                'passwordNew' => '',
            ]
            , 'POST');
        KujialeLogic::kujialePassword($this->verifyData);
        return;
    }

    public function KuJiaLe()
    {
        $this->verify(
            [
                'name' => '',
                'password' => '',
                'new' => 'no_required',
                'dest'=>''
            ]
            , 'POST');
        $appkey='qOpB5l0k70';
        $appsecret='IcAPNjgmmiEjGGgLWdwsh1Ton8zpscns';
        //$name=$this->verifyData['name'];
        //$password=$this->verifyData['password'];
        $dest = $this->verifyData['dest'];
        //账号
        //$kujiale = KujialeLogic::Kujiale($name,$password,$this->verifyData['new'] ?? '');
        $kujiale = KujialeLogic::loginKuJiaLe($this->verifyData);
        $timestamp=$this->get_subtraction();
        $appuid=$kujiale['keyId'];
        $sign = md5($appsecret.$appkey.$appuid.$timestamp);
        $url='http://www.kujiale.com/p/openapi/login?';
        $asd = [
            'appuid' => $appuid,
            'appuname' => $kujiale['userName'] ?? $kujiale['name'],
            'appuemail' => $kujiale['email'] ?? '',
            'appuphone' => $kujiale['phone'] ?? '',
            'appussn' => '',
            'appuaddr' => '',
            'appuavatar' => '',
            'appkey' => $appkey,
            'timestamp' => $timestamp,
            'sign' => $sign,
            'dest' => $dest,
            'apputype' => 0,
            //'designid' => '',
        ];

        $o = "";
        foreach ( $asd as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $asd = substr($o,0,-1);
        $res = json_decode($this->request_post($url,$asd));
        /*if($res->errorCode == 10002){
            throw new RJsonError('没有该账号', 'NOT_ACCOUNT');
        }
        if($res->errorCode == 10005){
            throw new RJsonError('请求过期', 'ERROR');
        }*/
        //$res = $this->send_post($url,$asd);
        return ['data'=>$res];
    }
    function get_subtraction()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
    public function http_post_data($url,$data_string)
    {
        var_dump($data_string);
        $ch = curl_init();// 创建一个新cURL资源
        curl_setopt($ch, CURLOPT_POST, 1);//设置请求为post;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);// 添加post数据到请求中
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//支持https请求
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//curl获取页面内容或提交数据，作为变量储存，而不是直接输出。
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=utf-8',
            'Content-Length: ' . strlen($data_string)));
        $return_content=curl_exec($ch);// 抓取URL并把它传递给浏览器
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $return_content;
    }
    public function test($params)
    {
        $uri='http://www.kujiale.com/p/openapi/login?' . http_build_query($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Verydows');
        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }
    public function testAction(){
        $url = 'http://mobile.jschina.com.cn/jschina/register.php';
        $post_data['appid']       = '10';
        $post_data['appkey']      = 'cmbohpffXVR03nIpkkQXaAA1Vf5nO4nQ';
        $post_data['member_name'] = 'zsjs123';
        $post_data['password']    = '123456';
        $post_data['email']    = 'zsjs123@126.com';
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $res = $this->request_post($url, $post_data);
        print_r($res);

    }


    public function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type:application/json;charset=utf-8',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }


}

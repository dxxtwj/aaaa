<?php
namespace App\Http\Controllers\V2\Api\User;
use App\Http\Controllers\Controller;
use App\Logic\V2\Common\WxSampleLogic;
use App\Logic\V2\User\WechatLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

class WechatController extends Controller
{
    /*private $appid = 'wx3c42f13a353c0d1c';
    private $secret = 'a8695f3a2e6e18e7b1f78f8fedeb9fd9';
    public function __construct(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
        if(!cookie('set_token')){
            $data = get_http($url);
            $data = json_decode($data);
            cookie('set_token',$data->access_token,7000);
        }
    }
    public function index(){
        //define your token
        define("TOKEN", "weixin");
        $Obj = new WxSampleLogic();
        if(!empty($_GET["echostr"])){
            $Obj->valid();
        }else{
            $Obj->responseMsg();
        }
    }*/
    public function TokenTest()
    {
        //WxSampleLogic::addToken('66666','888888888');
        $res = WxSampleLogic::getToken();
        return ['data'=>$res];
    }

    public function getWechatUrl()
    {
        $this->verify(
            [
                'wechatByUrl' => ''
            ]
            , 'GET');
        $url = WxSampleLogic::WechatUrl($this->verifyData['wechatByUrl']);
        return ['data'=>['wechatUrl'=>$url]];
    }

    public function getWechatCode()
    {
        $this->verify(
            [
                'code' => '',
                'state' => '',
            ]
            , 'GET');
        //$url = $this->verifyData['wechatUrl'];
        //$url="http://bnwh.cdn.easyke.top/web/lists/vote/1?code=061BzZNM0BMs042h11NM0ka3OM0BzZNi&state=p658jbv5ez8ygjsg";
        //$url2 = parse_url($url);
        //$arr = explode('&',$url2['query']);
        //$code = substr($arr[0],5);
        //$string = substr($arr[1],6);
        $code=$this->verifyData['code'];
        $string=$this->verifyData['state'];
        $uoaid=$this->getWechatToken($code);
        $res=[
            'uoaid'=>$uoaid,
            'key'=>$string,
        ];
        //添加到数据库
        WxSampleLogic::addOauthKey($res);
        return;
    }

    /**
     *
     */
    public function getWechatToken($code)
    {
        $config=WechatLogic::WeChatConfig();
        $appid = $config['appid'];
        $secret = $config['secret'];
        if(empty($code)){
            throw new RJsonError('获取微信信息失败', 'NOT_CODE');
        }
        //去数据了拿数据
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_token_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $json_obj = json_decode($res,true);
        if(empty($json_obj['access_token'])){
            throw new RJsonError('获取微信信息失败', 'NOT_TOKEN');
        }
        //存数据库access_token、openid
        WxSampleLogic::addToken($json_obj['access_token'],$json_obj['openid']);
        $access_token = $json_obj['access_token'];
        $openid = $json_obj['openid'];
       /* $res = WxSampleLogic::getToken();
        if(empty($res)){
        }else{
            $access_token=$res['token'];
            $openid =$res['openid'];
        }*/
        //根据openid和access_token查询用户信息
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $user_obj = json_decode($res,true);
        if(empty($user_obj)){
            throw new RJsonError('获取微信信息失败', 'NOT_WECHAT');
        }
        //查微信信息--没有就添加
        $wechat = WechatLogic::getWechatByOpenid($user_obj['openid']);
        if(empty($wechat)){
            //添加
            WechatLogic::addWechat($user_obj,$res);
            $wechat = WechatLogic::getWechatByOpenid($user_obj['openid']);
        }
        return $wechat['uoaid'];
    }

    //扫码后的状态
    public function WechatState()
    {
        $state=false;
        $res = WxSampleLogic::getWechatByKey();
        if(!empty($res)){
            $state=true;
        }
        $user=$this->getWechatByUoaid();
        $data['state']=$state;
        $data['isBind']=empty($user['data']['isBind'])? false :$user['data']['isBind'];
        return ['data'=>$data];
    }

    //获取微信的信息--如果绑定了就直接登录，没有就去绑定
    public function getWechatByUoaid()
    {
        $res = WxSampleLogic::getWechatByKey();
        \Session::put(['uoaid'=>$res['uoaid']]);
        $wechat = WechatLogic::getWechatByUoaid($res['uoaid']);
        if($wechat['uid']!=0){
            \Session::put(['uid'=>$wechat['uid']]);
            $userInfo['isBind']=true;
        }else{
            $userInfo = json_decode($wechat['jsondata'],true);
            $Info['avatar'] = $userInfo['headimgurl']??'';
            $Info['nickname'] = $userInfo['nickname']??'';
            $Info['sex'] = $userInfo['sex']??'0';
            \Session::put(['wechatInfo'=>$Info]);
            $userInfo['isBind']=false;
        }
        return ['data'=>$userInfo];
    }


}
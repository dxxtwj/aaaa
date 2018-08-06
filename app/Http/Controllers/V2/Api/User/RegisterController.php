<?php

namespace App\Http\Controllers\V2\Api\User;

use App\Logic\V2\Common\VerifyLogic;
use App\Logic\V2\Common\WxSampleLogic;
use App\Logic\V2\User\UserLogic;
use App\Logic\V2\User\WechatLogic;
use DdvPhp\DdvFile\Exception\Input;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Mail;

class RegisterController extends Controller
{
    //注册
    public function register()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
                'password' => '',
                //'codeImg' => '',
                'code' => ''
            ]
            , 'POST');
        UserLogic::Account($this->verifyData);
        return;
    }

    //判断是否存在
    public function existAccount()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
            ]
            , 'POST');
        UserLogic::existAccount($this->verifyData);
    }

    //获取图片验证码
    public function getImgVerify()
    {
        /*$this->validate(null, [
            'phone' => 'required|integer',
        ]);*/
        $code = VerifyLogic::generateVerifyCode(4);
        /*var_dump($code);*/
        $image = VerifyLogic::getImageBase64($code);
        return [
            'code'=>$code,
            'data'=>[
                'base64'=>$image
            ]
        ];
    }

    public function getImageVerify()
    {
        /*$this->validate(null, [
            'phone' => 'required|integer',
        ]);*/
        $code = VerifyLogic::generateVerifyCode(4);
        /*var_dump($code);*/
        $image = VerifyLogic::getImageBase64($code);
        return [
            'data'=>[
                'base64'=>$image
            ]
        ];
    }

    //发送验证码
    public function sendSms(Request $request)
    {
        $this->validate(null, [
            'phone' => 'required|integer',
        ]);
        //拿IP地址
        $request->setTrustedProxies(array('10.10.0.0/16'));
        $code = VerifyLogic::generateVerifyCode(4, '0123456789');
        //保存验证码
        VerifyLogic::addCode($this->verifyData['phone'],$code,1);
        //发送短信
        VerifyLogic::sendSmsVerify($this->verifyData['phone'],$code);
        return;
    }

    //获取邮箱的验证码
    public function getEmail()
    {
        $this->verify(
            [
                'email' => ''
            ]
            , 'POST');
        $data['code']=VerifyLogic::generateVerifyCode(4,'0123456789');
        //保存session
        \Session::put(['email'=>$this->verifyData['email']]);
        \Session::put(['emailCode'=>$data['code']]);
        $templateCode='emails.verifyCode';
        //获取发送人信息，配置邮箱配置
        //修改邮箱配置
        VerifyLogic::mailDeploy();
        /*\Config::set('mail.from', array('address' => '13592957850@163.com', 'name' => 'Name'));
        \Config::set('mail.username', '13592957850@163.com');
        \Config::set('mail.password', 'czs123456');*/
        $subject='获取验证码';
        VerifyLogic::sendEmailVerify($this->verifyData['email'],$data,$templateCode,$subject);
        return [
            'data'=>[]
        ];
    }

    //获取邮箱的验证码
    public function getEmailAnnex()
    {
        $this->verify(
            [
                'email' => ''
            ]
            , 'POST');
        $templateCode='emails.article';
        $data['article']='感谢您的参与，请查看附件了解相关信息。';
        //获取发送人信息，配置邮箱配置
        //修改邮箱配置
        VerifyLogic::mailDeploy();
        /*\Config::set('mail.from', array('address' => '13592957850@163.com', 'name' => 'Name'));
        \Config::set('mail.username', '13592957850@163.com');
        \Config::set('mail.password', 'czs123456');*/
        $subject='报名参赛反馈';
        VerifyLogic::sendEmailAnnex($this->verifyData['email'],$data,$templateCode,$subject);
        return [
            'data'=>[]
        ];
    }

    //点击微信登录
    public function SendWechat()
    {
        $this->verify(
            [
                'wechatByUrl' => ''
            ]
            , 'GET');
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8956bb1b3181f683&redirect_uri='.$this->verifyData['wechatByUrl'].'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
        /*else{
            $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8956bb1b3181f683&redirect_uri=http://bnj.bnwh.net/wap/page/signInWith&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
        }*/
        if(empty($url)){
            throw new RJsonError('参数错误', 'URL_ERROR');
        }
        $res['url']=$url;
        return ['data'=>$res];
    }

    //投票链接
    public function getWxByVote(){
        $this->verify(
            [
                //'wechatUrl' => '',
                'code'=>'',
                'state'=>'no_required',
            ]
            , 'GET');
        //$url = $this->verifyData['wechatUrl'];
        //$url="http://bnwh.cdn.easyke.top/web/lists/vote/1?code=071eiSCC0dyOvc2rixBC0YkXCC0eiSCA&state=1";
        /*$url2 = parse_url($url);
        $arr = explode('&',$url2['query']);*/
        $code = $this->verifyData['code'];
        $this->getTokenByVote($code);
    }
    public function getTokenByVote($code)
    {
        $config=WechatLogic::WeChatConfig();
        $appid = $config['appid'];
        $secret = $config['secret'];
        //$code = \Session::get('code');
        if(empty($code)){
            throw new RJsonError('获取微信信息失败', 'NOT_CODE');
        }
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
        //根据openid和access_token查询用户信息
        $access_token = $json_obj['access_token'];
        $openid = $json_obj['openid'];
        //存数据库access_token、openid
        /*WxSampleLogic::addToken($json_obj['access_token'],$json_obj['openid']);
        $res = WxSampleLogic::getToken();
        if(empty($res)){
        }else{
            $access_token=$res['token'];
            $openid =$res['openid'];
        }*/
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
        $userInfo['userAvatar'] = $user_obj['headimgurl']??'';
        $userInfo['userNickname'] = $user_obj['nickname']??'';
        $userInfo['sex'] = $user_obj['sex']??'0';
        \Session::put(['wechatInfo'=>$userInfo]);
        $userId=[];
        //去用户表查有没有注册过
        $USER = UserLogic::getUser($user_obj['openid']);
        if(!empty($USER)){
            //\Session::put(['uid'=>$USER['uid']]);
            $userId=$USER['uid'];
        }else{
            //检测微信信息有没有添加过--没有就添加
            $wechat = WechatLogic::getWechatByOpenid($user_obj['openid']);
            if(empty($wechat)){
                $user=[
                    'account'=>$user_obj['openid'],
                    'type'=>4,//微信
                    'registerTime'=>time(),
                ];
                $uid = UserLogic::addAffairByWechat($user,$userInfo,$user_obj,$res);
                //\Session::put(['uid'=>$uid]);
                $userId=$uid;
            }else{
                //如果添加过，看有没有绑定过，有就直接登录
                if($wechat['uid']!=0){
                    //\Session::put(['uid'=>$wechat['uid']]);
                    $userId=$wechat['uid'];
                }else{
                    $user=[
                        'account'=>$user_obj['openid'],
                        'type'=>4,//微信
                        'registerTime'=>time(),
                    ];
                    $uid = UserLogic::addAffairWechat($user,$userInfo);
                    //\Session::put(['uid'=>$uid]);
                    $userId=$uid;
                }
            }
        }
        \Session::put(['uid'=>$userId]);
        return;
    }

    public function getWx(Request $request)
    {
        $this->verify(
            [
                'code'=>'',
                'state'=>'no_required',
            ]
            , 'GET');
        //$url = $this->verifyData['wechatUrl'];
        //$wechatType = $this->verifyData['wechatType'];
        //$url = $request->header('Referer');
        //$url="http://bnj.bnwh.net/web/page/test?code=071cHN3B1JhxMe0iDp0B1qYL3B1cHN3N&state=1";
        //$url2 = parse_url($url);
        //$arr = explode('&',$url2['query']);
        $code = $this->verifyData['code'];
        return $this->getToken($code);
    }

    public function getToken($code)
    {
        $config=WechatLogic::WeChatConfig();
        $appid = $config['appid'];
        $secret = $config['secret'];
        //$code = \Session::get('code');
        if(empty($code)){
            throw new RJsonError('获取微信信息失败', 'NOT_CODE');
        }
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
        $access_token = $json_obj['access_token'];
        $openid = $json_obj['openid'];
        /*//存数据库access_token、openid
        $res = WxSampleLogic::getToken();
        WxSampleLogic::addToken($json_obj['access_token'],$json_obj['openid']);
        //根据openid和access_token查询用户信息
        if(empty($res)){
        }else{
            $access_token=$res['token'];
            $openid =$res['openid'];
        }*/
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
        $userInfo['avatar'] = $user_obj['headimgurl']??'';
        $userInfo['nickname'] = $user_obj['nickname']??'';
        $userInfo['sex'] = $user_obj['sex']??'0';
        \Session::put(['wechatInfo'=>$userInfo]);
        //查微信信息--没有就添加
        $wechat = WechatLogic::getWechatByOpenid($user_obj['openid']);
        if(empty($wechat)){
            //添加
            WechatLogic::addWechat($user_obj,$res);
            $wechat = WechatLogic::getWechatByOpenid($user_obj['openid']);
        }
        \Session::put(['uoaid'=>$wechat['uoaid']]);
        //判断有没有绑定
        if($wechat['uid']==0){
            $bind=false;
            $userInfo['isWechatBind']=$bind;
            $userInfo['wechatState']=false;
            return ['data'=>$userInfo];
        }else{
            //已经绑定过就直接登录
            \Session::put(['uid'=>$wechat['uid']]);
            return ['data'=>[
                'isWechatBind'=>true,
                'wechatState'=>true
            ]];
        }
    }

    //获取微信信息
    public function getWechat()
    {
        //从session拿信息
        $wechatInfo = \Session::get('wechatInfo');
        return ['data'=>$wechatInfo];

    }

    //绑定微信
    public function bindWechat()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
                'password' => ''
            ]
            , 'POST');
        $user = WechatLogic::bindWechat($this->verifyData);
        return ['data'=>$user];
    }

    //微信注册并绑定
    public function wechatRegister()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
                'code'=>'',
                'password' => '',
                'userNickname' => '',
                'userAvatar' => '',
                'sex' => 'no_required',
            ]
            , 'POST');
        WechatLogic::wechatRegister($this->verifyData);
        return;
    }

    //获取授权信息
    public function getMessageByUoaid()
    {
        $this->verify(
            [
                'authType'=>'',
            ]
            , 'GET');
        $res = UserLogic::getMessageByUoaid($this->verifyData);
        return ['data'=>$res];
    }
    //授权绑定
    public function authBind()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
                'password' => '',
                'authType'=>'',
            ]
            , 'POST');
        $user = WechatLogic::bind($this->verifyData);
        return ['data'=>$user];
    }
    //微博、QQ、注册并绑定
    public function authRegister()
    {
        $this->verify(
            [
                'account' => '',
                'type'=>'',
                'code'=>'',
                'password' => '',
                'userNickname' => '',
                'userAvatar' => '',
                'sex' => 'no_required',
            ]
            , 'POST');
        WechatLogic::wechatRegister($this->verifyData);
        return;
    }


}

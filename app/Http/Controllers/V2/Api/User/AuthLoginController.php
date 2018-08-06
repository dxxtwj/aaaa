<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/29
 * Time: 上午8:51
 */

namespace App\Http\Controllers\V2\Api\User;

use App\Logic\Site\DomainLogic;
use App\Logic\V2\User\OauthCallbackLogic;
use DdvPhp\DdvAuthOtherLogin;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Http\Request;


class AuthLoginController extends \App\Http\Controllers\Controller
{
    public function oauth(Request $request){
        $this->verify(
            [
                'url' => 'no_required',//分类id
            ]
            , 'GET');
        //$url='bnj.bnwh.net';
        if(empty($this->verifyData['url'])){
            $res = $_GET['redirect_uri'];
            $path = parse_url($res);
            $url = $path['host'];
        }else{
            $url=$this->verifyData['url'];
        }
        $siteId = DomainLogic::getDomain($url);
        DdvAuthOtherLogin::setConfig([
            'authUri'=>'http://api.shangrui.cc/v2.0/api/oauth'
        ]);

        // 设置回调逻辑
        OauthCallbackLogic::setCallback($ip = null,$siteId);

        // 如果是支付宝
        if ($_GET['type']==='alipay_web'){
            // 获取用户资料和用户基本信息
            $_GET['getscope'] = 'auth_base,auth_userinfo';
        }

        // 运行授权登录模块
        $res = DdvAuthOtherLogin::authLogin($_GET);
        // 判断是否在服务器跳转
        if (isset($res['redirectServer'])&&$res['redirectServer']==true){
            // 直接返回一个跳转给框架
            return redirect($res['url']);
        } else {
            // 返回数据给到前端
            return [
                'data'=>$res
            ];
        }
    }
}


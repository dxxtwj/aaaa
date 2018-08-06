<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\WechatLogin;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Login\LoginModel;
use App\Model\Shangrui\User\UserModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use JPush\Exceptions\JPushException;
use App\Http\Controllers\V1\Open\Wechat\EventController;

class WechatLoginLogic extends ShoppingLogic
{
//    const APPID = 'wx3d1656447b0c8f6a';
//    const APPID = 'wx4f3c8e39e32189b6';
//    const RESPONSETYPE = 'code';
//    const APPSECRET = 'e371443f0c000921d773b7b1dab8591d';
//    const SCOPE = 'snsapi_userinfo';
//
//    /*
//     * 获取code值
//     * @param array $data 回调地址
//     * @param string $returnUrl 自定义的参数，前段需要
//     * @param string $redirectUrl 回调地址
//     * reuturn string|array 已登录返回用户数据  否则 返回一个url地址
//     */
//    public static function login($data = array()){
//        $userId  = self::isLogin('userId');
//        \Log::info('isLogin'.json_encode($userId));
//        if (!empty($userId)){ //是登录状态
//            $isLogin['isLogin'] = true;
//            return ['data' => $isLogin];
//        } else { //没有登录
//            $returnUrl = urlencode($data['returnUrl']);
//            $redirectUrl = urlencode($data['returnUrl'].'?returnurl'.$returnUrl); //前端需要的参数
//            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPID.'&redirect_uri='.$redirectUrl.'&response_type='.self::RESPONSETYPE.'&scope='.self::SCOPE.'&state=123#wechat_redirect';
//
//            $array['url'] = $url;
//            $array['isLogin'] = false;
//            \Log::info(json_encode($array));
//            return ['data' => $array];
//        }
//
//    }
//
//    /*
//     * 拉取用户的信息
//     */
//    public static function redirect($code){
//        $userModel = new UserModel();
//        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::APPID."&secret=".self::APPSECRET."&code=".$code."&grant_type=authorization_code";
//        $res = self::http_curl($url, 'get');
//        $access_token = $res['access_token'];
//        $openid = $res['openid'];
//
//        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
//        $userInfo = self::http_curl($url);
//        if (empty($userInfo)){
//            throw new RJsonError('授权失败','NO_ACCOUNT_LOGIN');
//        } elseif (!empty($userInfo)){
//            $user = $userModel->where('user_openid',$openid)->firstHumpArray();
//            if (!empty($user)){
//                \Session::put('userId',$user['userId']);
//                return ['data' => $user,'isLogin' => 'true'];
//            }
//            $userData['user_nickname'] = $user['nikename'];
//            $userData['user_sex'] = $user['sex'];
//            $userData['user_country'] = $user['country'];
//            $userData['user_city'] = $user['city'];
//            $userData['user_openid'] = $user['openid'];
//            $userData['user_province'] = $user['province'];
//            $userData['user_code'] = $code;
//            $userData['access_token'] = $res['access_token'];
//            $userData['expires_in'] = $res['expires_in'];
//            $userData['refresh_token'] = $res['refresh_token'];
//            $userData['scope'] = $res['scope'];
//            $userData['user_headimgurl'] = $userInfo['headimgurl'];
//
//            $userModel->setDataByHumpArray($userData)->save();
//            $id = $userModel->getQueueableId();
//            \Session::put('userId',$id);
//            return ['data'=>$userData,'isLogin'=>true];
//        }
//    }
//
//    /*
//     * $url 接口 url string
//     * $type 请求类型 post||get
//     * $res 返回数据类型 string
//     * $arr post 请参数 string||array
//     */
//    public static function http_curl($url, $type = 'get', $res = 'json', $arr = ''){
//        // 1.开启curl
//        $ch = curl_init();
//
//        // 2.设置curl参数
//        curl_setopt($ch,CURLOPT_URL,1);
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  //不直接输出，把变量信息存储起来
//        if ($type = 'post'){
//            curl_setopt($ch,CURLOPT_POST,1);
//            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
//        } else {
//
//        }
//
//        // 3.采集信息
//        $output = curl_exec($ch);
//
//        // 4.关闭
//        curl_close($ch);
//
//        // 请求成功
//        return json_decode($output,true);
//    }
    //授权登录
    public static function login($data = array()){
        $userId  = self::isLogin('userId');
        if (!empty($userId)){ //是登录状态
            $isLogin['isLogin'] = true;
            return ['data' => $isLogin];
        } else {
            $jsCode = $data['jsCode'];
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx5a78d45de8fd7184&secret=1f3bc88a90f4ea35e630c121fdeadf21&js_code='.$jsCode.'&grant_type=authorization_code';
            $array = self::http_curl($url);
            \Log::info('授权登录返回的参数');
            \Log::info($array);
            if (!empty($array['openid']) && $array['openid'] != '40029'){
             $loginStatus = self::storeLogin($array);
                return ['isLogin' =>true];
            }
            throw new RJsonError('授权失败','NO_ACCOUNT_LOGIN');
        }
    }

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
    // 存储用户登录状态
    public static function storeLogin($array){
        $loginModel = new LoginModel();
        $loginData['openid'] = $array['openid'];
        $loginData['session_key'] = $array['session_key'];
        $loginId = $loginModel->where('openid',$array['openid'])->firstHumpArray();
        if (empty($loginId)){
            $loginModel->setDataByHumpArray($loginData)->save();
            $id =$loginModel->getQueueableId();

            //存入session
            \Session::put('userId',$id);
            return ['data'=>$loginData,'isLogin'=>true];
        } elseif (!empty($loginId)){
            //存入session
            \Session::put('userId',$loginId['loginId']);
            return ['data'=>$loginData,'isLogin'=>true];
        }

    }

}


<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V2\User;
use App\Http\Middleware\ClientIp;
use App\Model\V2\User\OauthModel;
use DdvPhp\DdvAuthOtherLogin;
use DdvPhp\DdvException;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class OauthCallbackLogic
{
    /**
     * 所有授权登录回调
     * @param string $ip
     */
    public static function setCallback($ip = null,$siteId){
        if (empty($ip)){
            $ip = ClientIp::get();
        }
        // 微信授权回调绑定
        self::setWechatMpCallback($ip);
        // 支付宝授权回调绑定
        self::setAlipayWebCallback($ip);
        // QQ互联授权回调绑定
        self::setQqConnectWebCallback($ip,$siteId);
        // 新浪微博授权回调绑定
        self::setWeiboWebCallback($ip,$siteId);
    }

    /**
     * 微博授权登录回调
     * @param string $ip
     */
    public static function setWeiboWebCallback($ip,$siteId){
        // 获取微博配置
        $weiboConfig = config('weibo.web');
        // 微博web授权登录回调
        DdvAuthOtherLogin::setAuthCallBack(
        // 微博web授权类型
            'weibo_web',
            // 支付宝配置参数
            $weiboConfig,
            /**
             * 使用非静默授权模式
             */
            function ($userInfo, $tokenArray) use ($ip,$siteId){
                /**
                 * $userInfo['errorCode'] 错误码
                 * $userInfo['errorMessage'] 错误信息
                 * $userInfo['usersShow'] 变量参考 http://open.weibo.com/wiki/2/users/show
                 */
                if (empty($userInfo)||empty($userInfo['uid'])||empty($userInfo['usersShow'])){
                    return false;
                }

                $res = null;
                // 用户资料-静默授权方式获取
                try{
                    // 试图判断是否有这个微信公众平台的用户openid的数据在数据库
                    $res = OauthLogic::getOneByTypeOpenid(41, $userInfo['uid'],$siteId);
                }catch (DdvException $e){
                    $res = null;
                }
                if (empty($res)){
                    $info = $userInfo['usersShow'];
                    $userInfo['nickName'] = $info['screen_name']??$info['name']??'';
                    $userInfo['avatar'] = $info['avatar_hd']??$info['avatar_large']??$info['profile_image_url']??'';
                    $userInfo['sex'] = empty($info['gender'])?0:($info['gender']=='m'?1:($info['gender']=='f'?2:0));

                    // 用户资料-授权方式获取

                    $uoaid = OauthLogic::putOneByTypeOpenid(41, $userInfo['uid'], [
                        'unionid'=>'',
                        'unionType'=>4,
                        'jsondata'=>json_encode($userInfo)??'',
                        'lastLoginIp'=>$ip
                    ],$siteId);
                }else{
                    $uoaid = $res->uoaid;
                    // 试图登录指定uid
                    self::loginByUid($res->uid, $ip);
                }

                // 设定授权标识到会话中
                OauthLogic::setSessionUoaid($uoaid);
                // 试图登录指定uid
                return true;
            },
            /**
             * 静默授权逻辑
             * 如果返回false 系统将自动再次跳转支付宝使用非静默授权
             */
            function ($baseInfo) use ($ip,$siteId) {

                if (empty($baseInfo['uid'])){
                    // 如果没有拿到微博返回的用户id就，返回false，要求授权登录
                    return false;
                }
                // 用户资料-静默授权方式获取
                try{
                    // 试图判断是否有这个微博的用户uid的数据在数据库
                    $res = OauthLogic::getOneByTypeOpenid(41, $baseInfo['uid'],$siteId);
                }catch (DdvException $e){
                    // 查询后发现没有，只能要求重新强制授权了
                    return false;
                }
                if ($res->isReload != 0){
                    // 这个强制要求重新授权
                    return false;
                }
                // 设定授权标识到会话中
                OauthLogic::setSessionUoaid($res->uoaid);

                // 试图登录指定uid
                self::loginByUid($res->uid, $ip);
                return true;
            }
        );
    }

    /**
     * QQ互联授权登录回调
     * @param string $ip
     */
    public static function setQqConnectWebCallback($ip,$siteId){

        // 获取QQ互联配置
        $qqConfig = config('qq.connect.web');
        //var_dump($qqConfig);
        // QQ互联授权登录回调
        DdvAuthOtherLogin::setAuthCallBack(
        // QQ互联web授权类型
            'qq_connect_web',
            // 支付宝配置参数
            $qqConfig,
            /**
             * 使用非静默授权模式
             */
            function ($userInfo) use ($ip,$siteId){
                if (empty($userInfo)||empty($userInfo['openid'])||empty($userInfo['scopeGetUserInfo'])){
                    return false;
                }

                $res = null;
                // 用户资料-静默授权方式获取
                try{
                    // 试图判断是否有这个微信公众平台的用户openid的数据在数据库
                    $res = OauthLogic::getOneByTypeOpenid(31, $userInfo['openid'],$siteId);
                }catch (DdvException $e){
                    try{
                        // 试图判断是否有这个微信公众平台的用户unionid的数据在数据库
                        $res = OauthLogic::getOneByTypeUnionid(3, $userInfo['unionid'],$siteId);
                    }catch (DdvException $e){
                        // 查询后发现没有，只能要求重新强制授权了
                        $res = null;
                    }
                }

                if (empty($res)){
                    $info = $userInfo['scopeGetUserInfo'];
                    $userInfo['nickName'] = $info['nickname']??'';
                    $userInfo['avatar'] = $info['figureurl_qq_2']??$info['figureurl_qq_1']??$info['figureurl_qq_2']??$info['figureurl_2']??$info['figureurl_1']??$info['figureurl']??'';
                    $userInfo['sex'] = empty($info['gender'])?0:($info['gender']=='男'?1:($info['gender']=='女'?2:0));

                    // 用户资料-授权方式获取

                    $uoaid = OauthLogic::putOneByTypeOpenid(31, $userInfo['openid'], [
                        'unionid'=>$userInfo['unionid']??'',
                        'unionType'=>3,
                        'jsondata'=>json_encode($userInfo)??'',
                        'lastLoginIp'=>$ip
                    ],$siteId);
                }else{
                    $uoaid = $res->uoaid;
                    // 试图登录指定uid
                    self::loginByUid($res->uid, $ip);
                }

                // 设定授权标识到会话中
                OauthLogic::setSessionUoaid($uoaid);
                // 试图登录指定uid
                return true;
            }
        );

    }

    /**
     * 微信授权登录回调
     * @param string $ip
     */
    public static function setWechatMpCallback($ip){
        //修改微信配置
        //UserLogic::editConfig();
        // 获取支付宝配置
        $mpConfig = config('wechat.mp');
        // 微信授权登录回调
        DdvAuthOtherLogin::setAuthCallBack(
            // 微信公众平台授权类型
            'wechat_mp',
            // 支付宝配置参数
            $mpConfig,
            /**
             * 使用非静默授权模式
             */
            function ($userInfo) use ($ip){
                // 用户资料-授权方式获取
                if (!empty($userInfo)){
                    $userInfo['avatar'] = $userInfo['nick_name']??'';
                    $userInfo['nickName'] = $userInfo['nick_name']??'';
                    $userInfo['sex'] = $userInfo['sex']??'0';
                    $uoaid = OauthLogic::putOneByTypeOpenid(21, $userInfo['openid'], [
                        'unionid'=>$userInfo['unionid']??'',
                        'unionType'=>2,
                        'jsondata'=>json_encode($userInfo)??'',
                        'lastLoginIp'=>$ip
                    ]);
                    // 设定授权标识到会话中
                    OauthLogic::setSessionUoaid($uoaid);
                    // 试图登录指定uid
                    return true;
                }
            },
            /**
             * 静默授权逻辑
             * 如果返回false 系统将自动再次跳转支付宝使用非静默授权
             */
            function ($baseInfo) use ($ip){
                if (empty($baseInfo['openid'])){
                    // 如果没有拿到支付宝返回的用户id就，返回false，要求授权登录
                    return false;
                }
                // 用户资料-静默授权方式获取
                try{
                    // 试图判断是否有这个微信公众平台的用户openid的数据在数据库
                    $res = OauthLogic::getOneByTypeOpenid(21, $baseInfo['openid']);
                }catch (DdvException $e){
                    try{
                        // 试图判断是否有这个微信公众平台的用户unionid的数据在数据库
                        $res = OauthLogic::getOneByTypeUnionid(2, $baseInfo['unionid']);
                    }catch (DdvException $e){
                        // 查询后发现没有，只能要求重新强制授权了
                        return false;
                    }
                }
                if ($res->isReload != 0){
                    // 这个强制要求重新授权
                    return false;
                }
                // 设定授权标识到会话中
                OauthLogic::setSessionUoaid($res->uoaid);

                // 试图登录指定uid
                self::loginByUid($res->uid, $ip);
                return true;
            }
        );

    }

    /**
     * 支付宝授权登录回调
     * @param string $ip
     */
    public static function setAlipayWebCallback($ip){

        // 获取支付宝配置
        $alipayConfig = config('alipay.openApi');
        // 支付宝授权登录回调
        DdvAuthOtherLogin::setAuthCallBack(
        // 支付宝web授权类型
            'alipay_web',
            // 支付宝配置参数
            $alipayConfig,
            /**
             * 使用非静默授权模式
             */
            function ($resData) use ($ip){
                // 用户资料-授权方式获取
                if (!empty($resData['scopeAuthUserinfo'])){
                    $data = [
                        'more'=>[]
                    ];
                    $scope = $resData['scope']??[];
                    foreach ($scope as $key){
                        if (!empty($resData['scope_'.$key])){
                            $data['more'][$key] = $resData['scope_'.$key];
                        }
                    }
                    $uf = $resData['scopeAuthUserinfo']??[];
                    $data['userId'] = $resData['userId'];
                    $data['alipayUserId'] = $resData['alipayUserId'];
                    $data['avatar'] = $uf['avatar']??'';
                    $data['nickName'] = $uf['nick_name']??'';
                    $data['sex'] = empty($uf['gender'])?0:($uf['gender']=='m'?1:($uf['gender']=='f'?2:0));

                    $uoaid = OauthLogic::putOneByTypeOpenid(11, $resData['userId'], [
                        'unionid'=>'',
                        'unionType'=>1,
                        'jsondata'=>json_encode($data),
                        'lastILoginIp'=>$ip
                    ]);

                    // 设定授权标识到会话中
                    OauthLogic::setSessionUoaid($uoaid);
                    // 试图登录指定uid
                    return true;
                }
            },
            /**
             * 静默授权逻辑
             * 如果返回false 系统将自动再次跳转支付宝使用非静默授权
             */
            function ($baseInfo) use ($ip){
                if (empty($baseInfo['userId'])){
                    // 如果没有拿到支付宝返回的用户id就，返回false，要求授权登录
                    return false;
                }
                // 用户资料-静默授权方式获取
                try{
                    // 试图判断是否有这个支付宝的用户userId的数据在数据库
                    $res = OauthLogic::getOneByTypeOpenid(11, $baseInfo['userId']);
                }catch (DdvException $e){
                    // 查询后发现没有，只能要求重新强制授权了
                    return false;
                }
                if ($res->isReload != 0){
                    // 这个强制要求重新授权
                    return false;
                }
                // 设定授权标识到会话中
                OauthLogic::setSessionUoaid($res->uoaid);

                // 试图登录指定uid
                self::loginByUid($res->uid, $ip);
                return true;
            }
        );

    }

    /**
     * @param int $uid
     * @param string $ip
     */
    public static function loginByUid($uid, $ip) {
        if (empty($uid)){
            return;
        }
        try{
            LoginLogic::setLogin($uid, $ip);
        }catch (DdvException $e){

        }
    }
}

















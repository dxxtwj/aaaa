<?php

namespace App\Logic\V1\Open\Wechat;

/*
 * 这个类是微信小程序的逻辑层
 */
use App\Http\Controllers\V1\Open\Wechat\EventController;
use App\Http\Controllers\V3\Api\Web\Open\AuthController;
use App\Model\Shopping\Shop\ShopModel;
use App\Model\V3\WechatAuthorizer\WechatAuthorizerModel;
use App\Model\V3\WechatAuthorizer\WechatTemplateModel;

class SmallPogramLogic
{



// ----------------------------------  第三方平台的权限

    /*
     * 第三方平台可以使用接口拉取当前所有已授权的帐号基本信息。
     */
    public static function getJurisdiction($data) {
        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台的
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_list?component_access_token='.$componentAccessToken;

        $data = [

            'component_appid' => $eventController::APPID,
            'offset' => $data['offset'],
            'count' => $data['count'],

        ];
        $jsonParam = json_encode($data, true);
        $array = json_decode(self::curl($url, 'post', $jsonParam),true);


        return ['lists' => $array['list']];
    }



//---------------------------------小程序基本信息设置-----------------------------



    /*
     * 1 获取帐号基本信息
     */
    public static function getUserInfo($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/account/getaccountbasicinfo?access_token='.$wechatAuthorizerAccessToken;

        $array = self::curl($url);

        return $array;
    }



    /*
     * 2 小程序名称设置及改名
     */
    public static function editUserInfo($data) {

//        $authController = new AuthController();
//        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
//        $url = 'https://api.weixin.qq.com/wxa/setnickname?access_token='.$wechatAuthorizerAccessToken;
//        $param = [
//
//            'nick_name' => 'asdasdasd'
//        ];
//        $array = self::curl($url,'post',json_encode($param, true));
//        return $array;
    }

// ----------------------------代码管理---------------------



    /*
     * 1、为授权的小程序帐号上传小程序代码
     */
    public static function upload($data) {


        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/commit?access_token='.$wechatAuthorizerAccessToken;
        $param = [

            'template_id' => $data['templateId'],//代码库中的代码模版ID
            'ext_json' => '{"testdata":"易思客"}',//第三方自定义的配置
            'user_version' => $data['userVersion'],//代码版本号，开发者可自定义
            'user_desc' => $data['userDesc'],//代码描述，开发者可自定义
        ];
        $jsonParam = json_encode($param);
        $arr = json_decode(self::curl($url, 'post',$jsonParam), true);

        $errorMsg = self::getErrorCode($arr['errcode']);
        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        $wechatAuthorizerModel = new WechatAuthorizerModel();
        $where['wechat_authorizer_appid'] = $data['authorizerAppid'];
        $status['wechat_small_program_status'] = 1;//已上传代码
        $wechatAuthorizerModel->where($where)->updateByHump($status);

        return ['data' => $errorMsg];


    }

    /*
     * 2、获取体验小程序的体验二维码
     */
    public static function getCode($data) {
        header('Content-Type:image/png');
        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $urlencode = urlencode($data['path']);
        $url = 'https://api.weixin.qq.com/wxa/get_qrcode?access_token='.$wechatAuthorizerAccessToken.'&path='.$urlencode;
        $code = self::curl($url,'get',array(),0);
        return $code;
    }


    /*
     * 3、获取授权小程序帐号的可选类目
     */
    public static function getCategory($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/get_category?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url), true);

        $errorMsg = self::getErrorCode($arr['errcode']);
        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['lists' => $arr['category_list']];
    }


    /*
     * 4、获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
     */
    public static function getConfig($data) {
        $authController = new AuthController();

        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);

        $url = 'https://api.weixin.qq.com/wxa/get_page?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url), true);

        $errorMsg = self::getErrorCode($arr['errcode']);
        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        return ['lists' => $arr['page_list']];
    }


    /*
     * 5、将第三方提交的代码包提交审核（仅供第三方开发者代小程序调用）
     */
    public static function examine($data) {


        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/submit_audit?access_token='.$wechatAuthorizerAccessToken;
        $itemList['item_list'] = $data['itemList'];
        $jsonParam = json_encode($itemList,JSON_UNESCAPED_UNICODE);
        $jsonParam = str_replace("\\/", "/",  $jsonParam);
        $arr = json_decode(self::curl($url, 'post',$jsonParam), true);

        $errorMsg = self::getErrorCode($arr['errcode']);
        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        $wechatAuthorizerModel = new WechatAuthorizerModel();
        $where['wechat_authorizer_appid'] = $data['authorizerAppid'];
        $status['wechat_small_program_status'] = 2;//审核中
        $wechatAuthorizerModel->where($where)->updateByHump($status);

        return ['data' => $arr];
    }

    /*
     * 7、查询某个指定版本的审核状态（仅供第三方代小程序调用）
     */
    public static function getStatus($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/get_auditstatus?access_token='.$wechatAuthorizerAccessToken;

        $param = [

            'auditid' => $data['auditid'],
        ];
        $jsonParam = json_encode($param, true);
        $arr = json_decode(self::curl($url, 'post', $jsonParam));


        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }


        return ['data' => $arr];
    }

    /*
     * 8、查询最新一次提交的审核状态（仅供第三方代小程序调用）
     */
    public static function getNewStatus($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url), true);

        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $arr];
    }

    /*
     * 9、发布已通过审核的小程序（仅供第三方代小程序调用）
     */
    public static function release($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/release?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url,'post','{}'), true);

        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        $wechatAuthorizerModel = new WechatAuthorizerModel();
        $where['wechat_authorizer_appid'] = $data['authorizerAppid'];
        $status['wechat_small_program_status'] = 4;//发布成功
        $wechatAuthorizerModel->where($where)->updateByHump($status);

        return ['data' => $arr];
    }

    /*
     * 10、修改小程序线上代码的可见状态（仅供第三方代小程序调用）
     */
    public static function editStatus($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/change_visitstatus?access_token='.$wechatAuthorizerAccessToken;
        $param = [
            'action' => $data['action'],
        ];
        $jsonParam = json_encode($param, true);
        $arr = json_decode(self::curl($url,'post',$jsonParam), true);
        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $arr];
    }

    /*
     * 11. 小程序版本回退（仅供第三方代小程序调用）
     */
    public static function withdraw($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/revertcoderelease?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url), true);
        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $arr];
    }


    /*
     * 12. 查询当前设置的最低基础库版本及各版本用户占比 （仅供第三方代小程序调用）
     */
    public static function proportion($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/getweappsupportversion?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url,'post','{}'), true);
        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        $wechatAuthorizerModel = new WechatAuthorizerModel();
        $where['wechat_authorizer_appid'] = $data['authorizerAppid'];
        $status['wechat_small_program_status'] = 1;//已上传代码
        $wechatAuthorizerModel->where($where)->updateByHump($status);

        return ['data' => $arr];
    }


    /*
     * 15. 小程序审核撤回
     */
    public static function smallProgramWithdraw($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/undocodeaudit?access_token='.$wechatAuthorizerAccessToken;
        $arr = json_decode(self::curl($url), true);
        $errorMsg = self::getErrorCode($arr['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $arr];
    }



// -------------------------------------成员管理----------------------------------




    /*
     * 1、绑定微信用户为小程序体验者
     */
    public static function binding($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/bind_tester?access_token='.$wechatAuthorizerAccessToken;
        $bindingData['wechatid'] = $data['wechatid'];
        $jsonBinding = json_encode($bindingData, true);
        $array = json_decode(self::curl($url, 'post', $jsonBinding), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $array];
    }

    /*
     * 2、解除绑定小程序的体验者
     */
    public static function unsetBinding($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/unbind_tester?access_token='.$wechatAuthorizerAccessToken;
        $bindingData['wechatid'] = $data['wechatid'];
        $jsonBinding = json_encode($bindingData, true);
        $array = json_decode(self::curl($url, 'post', $jsonBinding), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $array];

    }

    /*
     * 3. 获取体验者列表
     */
    public static function getBinding($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/memberauth?access_token='.$wechatAuthorizerAccessToken;
        $bindingData['action'] = 'get_experiencer';
        $jsonBinding = json_encode($bindingData, true);
        $array = json_decode(self::curl($url, 'post', $jsonBinding), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['lists' => $array['members']];

    }






    //--------------------------------------小程序代码模版库管理-----------------



    /*
     * 1、获取草稿箱内的所有临时代码草稿
     */
    public static function library() {
        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];//第三方平台的
        $url = 'https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token='.$componentAccessToken;
        $array = json_decode(self::curl($url),true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['lists' => $array['draft_list']];
    }

    /*
     * 2、获取代码模版库中的所有小程序代码模版
     */
    public static function template() {

        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/wxa/gettemplatelist?access_token='.$componentAccessToken;
        $array = json_decode(self::curl($url),true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        return ['lists' => $array['template_list']];
    }

    /*
     * 3、将草稿箱的草稿选为小程序代码模版
     */
    public static function setUp($data) {

        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/wxa/addtotemplate?access_token='.$componentAccessToken;
        $wechatArray = [

            'draft_id' => $data['draftId']
        ];
        $jsonWechat = json_encode($wechatArray, true);
        $array = json_decode(self::curl($url,'post',$jsonWechat),true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $array];
    }

    /*
     * 4、删除指定小程序代码模版
     */
    public static function deleteTemplate($data) {

        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/wxa/deletetemplate?access_token='.$componentAccessToken;
        $wechatArray = [

            'template_id' => $data['templateId']
        ];
        $jsonWechat = json_encode($wechatArray, true);
        $array = json_decode(self::curl($url,'post',$jsonWechat),true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $array];
    }



    /*
     * 1 、 创建 开放平台帐号并绑定公众号/小程序
     */
    public static function addPlatform($data) {
        $eventController = new EventController();
        $componentAccessToken = $eventController->getComponentAccessToken()['component_access_token'];
        $url = 'https://api.weixin.qq.com/cgi-bin/open/create?access_token='.$componentAccessToken;
    }



    /*
     * 2 、 将 公众号/小程序绑定到开放平台帐号下
     */
    public static function bindingSmallProgram($data) {

        $authController = new AuthController();
        $eventController = new EventController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/cgi-bin/open/bind?access_token='.$wechatAuthorizerAccessToken;
        $component_appid = $eventController::APPID;

        $param = [

            'open_appid' => $component_appid,
            'appid' => $data['authorizerAppid'],

        ];

        $jsonArray = json_encode($param, true);
        $array = json_decode(self::curl($url,'post',$jsonArray), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }
        return ['data' => $array];

    }



// ---------------------------------- 修改服务器地址------------------------------------

    /*
     * 1、设置小程序服务器域名
    */
    public static function addServerDomain($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/modify_domain?access_token='.$wechatAuthorizerAccessToken;
        $action = strtolower($data['action']);
        $param['action'] = $action;
        if ($action != 'get') {
            $param = [
                'requestdomain' => $data['requestdomain'],
                'wsrequestdomain' => $data['wsrequestdomain'],
                'uploaddomain' => $data['uploaddomain'],
                'downloaddomain' => $data['downloaddomain'],
                'action' => $action,
            ];
        }

        $jsonParam = str_replace("\\/", "/",  json_encode($param, true));
        $array = json_decode(self::curl($url, 'post', $jsonParam), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        return ['data' => $array];

    }


    /*
     * 2、设置小程序业务域名（仅供第三方代小程序调用）
     */
    public static function addBusinessDomain($data) {

        $authController = new AuthController();
        $wechatAuthorizerAccessToken = $authController->getAuthorizerAccessToken($data);
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token='.$wechatAuthorizerAccessToken;
        $action = empty($data['action']) ? '' : strtolower($data['action']);

        if (!empty($action) && $action != 'get') {
            $param = [
                'webviewdomain' => $data['webviewdomain'],
                'action' => $action,
            ];
        }
        if (!empty($param)) {

            $jsonParam = str_replace("\\/", "/",  json_encode($param, true));
        } else {
            $jsonParam = '{}';
        }

        $array = json_decode(self::curl($url, 'post', $jsonParam), true);
        $errorMsg = self::getErrorCode($array['errcode']);

        if ($errorMsg['code'] != 0) {
            return ['data' => $errorMsg];
        }

        return ['data' => $array];
    }



// -----------------------------自己写的API，不是微信的--------

    /*
     * 获取小程序列表
     */
    public static function getSmallProgram($data) {
        $authModel = new WechatAuthorizerModel();
        if (empty($data['authorizerAppid'])) {
            $res = $authModel->orderBy('created_at', 'DESC')->getDdvPageHumpArray();
            return $res;

        } else {
            $res = $authModel->where('wechat_authorizer_appid', $data['authorizerAppid'])->firstHumpArray();
            $authController = new AuthController();
            $userInfo = $authController->getAuthUserInfo($res['wechatAuthorizerAppid']);
            $userInfo['wechatSmallProgramStatus'] = $res['wechatSmallProgramStatus'];
            $errorMsg = self::getErrorCode($userInfo['errcode']);
            if ($errorMsg['code'] != 0) {
                return ['data' => $errorMsg];
            }

            return ['data' => $userInfo];
        }
    }

    public static function aa($data) {


      $shop = new ShopModel();
      $a = $shop->get()->toArray();
      $a= $shop->first();

//      var_dump($a['/**/']);


    }


    /*
     * 删除小程序
     */
    public static function deleteSmallProgram($data) {

        $authModel = new WechatAuthorizerModel();
        $authModel->where('wechat_authorizer_appid', $data['authorizerAppid'])->delete();
        return ;
    }

    /*
     * @param string $error 错误码
     * @return array
     */
    public static function getErrorCode($error) {

        switch ($error) {
            case -1:
                $array['code'] = -1;
                $array['content'] = '系统繁忙';
                break;
            case 85013:
                $array['code'] = 85013;
                $array['content'] = '无效的自定义配置';
                break;
            case 40097:
                $array['code'] = 40097;
                $array['content'] = '可能是参数错误';
                break;
            case 85014:
                $array['code'] = 85014;
                $array['content'] = '无效的模版编号';
                break;
            case 85043:
                $array['code'] = 85043;
                $array['content'] = '模版错误';
                break;
            case 85044:
                $array['code'] = 85044;
                $array['content'] = '代码包超过大小限制';
                break;
            case 85045:
                $array['code'] = 85045;
                $array['content'] = 'ext_json有不存在的路径';
                break;
            case 85046:
                $array['code'] = 85046;
                $array['content'] = 'tabBar中缺少path';
                break;
            case 85047:
                $array['code'] = 85047;
                $array['content'] = 'pages字段为空';
                break;
            case 85048:
                $array['code'] = 85048;
                $array['content'] = 'ext_json解析失败';
                break;

            case 86000:
                $array['code'] = 86000;
                $array['content'] = '不是由第三方代小程序进行调用';
                break;
            case 86001:
                $array['code'] = 86001;
                $array['content'] = '不存在第三方的已经提交的代码';
                break;

            case 85006:
                $array['code'] = 85006;
                $array['content'] = '标签格式错误';
                break;
            case 85007:
                $array['code'] = 85007;
                $array['content'] = '页面路径错误';
                break;
            case 85008:
                $array['code'] = 85008;
                $array['content'] = '类目填写错误';
                break;
            case 85009:
                $array['code'] = 85009;
                $array['content'] = '已经有正在审核的版本';
                break;
            case 85010:
                $array['code'] = 85010;
                $array['content'] = 'item_list有项目为空';
                break;
            case 85011:
                $array['code'] = 85011;
                $array['content'] = '标题填写错误';
                break;
            case 85015:
                $array['code'] = 85015;
                $array['content'] = '该账号不是小程序账号';
                break;
            case 85016:
                $array['code'] = 85016;
                $array['content'] = '域名数量超过限制';
                break;
            case 85017:
                $array['code'] = 85017;
                $array['content'] = '没有新增域名，请确认小程序已经添加了域名或该域名是否没有在第三方平台添加';
                break;
            case 85018:
                $array['code'] = 85018;
                $array['content'] = '域名没有在第三方平台设置';
                break;
            case 85023:
                $array['code'] = 85023;
                $array['content'] = '审核列表填写的项目数不在1-5以内';
                break;
            case 85077:
                $array['code'] = 85077;
                $array['content'] = '小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）';
                break;
            case 86002:
                $array['code'] = 86002;
                $array['content'] = '小程序还未设置昵称、头像、简介。请先设置完后再重新提交。';
                break;
            case 85085:
                $array['code'] = 85085;
                $array['content'] = '近7天提交审核的小程序数量过多，请耐心等待审核完毕后再次提交';
                break;
            case 85086:
                $array['code'] = 85086;
                $array['content'] = '提交代码审核之前需提前上传代码';
                break;
            case 85012:
                $array['code'] = 85012;
                $array['content'] = '无效的审核id';
                break;
            case 85019:
                $array['code'] = 85019;
                $array['content'] = '没有审核版本';
                break;
            case 85020:
                $array['code'] = 85020;
                $array['content'] = '审核状态未满足发布';
                break;
            case 85021:
                $array['code'] = 85021;
                $array['content'] = '状态不可变';
                break;
            case 85022:
                $array['code'] = 85022;
                $array['content'] = 'action非法';
                break;
            case 87011:
                $array['code'] = 87011;
                $array['content'] = '现网已经在灰度发布，不能进行版本回退';
                break;
            case 87012:
                $array['code'] = 87012;
                $array['content'] = '该版本不能回退，可能的原因：1:无上一个线上版用于回退 2:此版本为已回退版本，不能回退 3:此版本为回退功能上线之前的版本，不能回退';
                break;
            case 85015:
                $array['code'] = 85015;
                $array['content'] = '版本输入错误';
                break;
            case 85079:
                $array['code'] = 85079;
                $array['content'] = '小程序没有线上版本，不能进行灰度';
                break;

            case 85080:
                $array['code'] = 85080;
                $array['content'] = '小程序提交的审核未审核通过';
                break;
            case 85081:
                $array['code'] = 85081;
                $array['content'] = '无效的发布比例';
                break;
            case 85082:
                $array['code'] = 85082;
                $array['content'] = '当前的发布比例需要比之前设置的高';
                break;
            case 85001:
                $array['code'] = 85001;
                $array['content'] = '微信号不存在或微信号设置为不可搜索';
                break;
            case 85002:
                $array['code'] = 85002;
                $array['content'] = '小程序绑定的体验者数量达到上限';
                break;
            case 85003:
                $array['code'] = 85003;
                $array['content'] = '微信号绑定的小程序体验者达到上限';
                break;
            case 85004:
                $array['code'] = 85004;
                $array['content'] = '微信号已经绑定';
                break;
            case 85064:
                $array['code'] = 85064;
                $array['content'] = '找不到模版或草稿';
                break;
            case 85065:
                $array['code'] = 85065;
                $array['content'] = '模版库已满';
                break;
            case 0:
                $array['code'] = 0;
                $array['content'] = '成功';
                break;
            case 89019:
                $array['code'] = 89019;
                $array['content'] = '业务域名无更改，无需重复设置';
                break;
            case 89020:
                $array['code'] = 89020;
                $array['content'] = '尚未设置小程序业务域名，请先在第三方平台中设置小程序业务域名后在调用本接口';
                break;
            case 89021:
                $array['code'] = 89021;
                $array['content'] = '请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名';
                break;
            case 89029:
                $array['code'] = 89029;
                $array['content'] = '业务域名数量超过限制';
                break;
            case 89231:
                $array['code'] = 89231;
                $array['content'] = '个人小程序不支持调用setwebviewdomain 接口';
                break;
            default :
                $array['code'] = -2;
                $array['content'] = '不明觉厉的错误';
                break;
        }

        return $array;
    }

    /*
      * CURL 请求  post || get 都可以
      * @param string $url 地址
      * @param string $type post|get 必须小写
      * @param array post数据
      * @param int $code 是否文件流  1 否   0 是
    */
    public static function curl($url, $type='get', $data=array(), $code=1) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if ($code == 1) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//存储变量
        }

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
}
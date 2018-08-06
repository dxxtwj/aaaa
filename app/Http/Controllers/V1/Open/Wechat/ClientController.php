<?php
// 这个是旧方法
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace App\Http\Controllers\V1\Open\Wechat;
use App\Http\Controllers\Controller;
use App\Logic\Exception;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use App\Model\V1\Open\OpenAppletsModel;
use App\Model\V1\Open\OpenAppWechatModel;
use EasyWeChat\Factory;
class ClientController extends Controller
{
    /*
     * 1、为授权的小程序帐号上传小程序代码
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */
    public function Commit()
    {
        $this->validate(null, [
            'appId'=>'required|string',
            'refreshToken'=>'required|string',
            'templateId' =>'required|integer',
            'extJson' =>'required|string',
            'userVersion' => 'required|string',
            'userDesc' => 'required|string',
        ]);
        $wechat = $this->verifyData;
        //获取refreshToken
        /*$authLogic = new OpenAppWechatLogic([
            'wechatAppId'=> $wechat['appId'],
        ]);
        $wechatInfo = $authLogic->getWechatInfo();*/
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['refreshToken']);
        //$miniProgram = $openPlatform->miniProgram('wxd2a3d0cfe6a572f5','refreshtoken@@@b0plr4NH9HAXD6rxQwLEYgaqqYYF98_MWbfjvKKGlt4');
        //$res = $miniProgram->code->commit('16', '{"testdata":"广用机械"}', 'v1.0', '这是广用机械');
        $res = $miniProgram->code->commit($wechat['templateId'], $wechat['extJson'], $wechat['userVersion'], $wechat['userDesc']);
        if($res['errcode']== -1){
            throw new Exception('系统繁忙', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85013){
            throw new Exception('无效的自定义配置', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85014){
            throw new Exception('无效的模版编号', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85043){
            throw new Exception('模版错误', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85044){
            throw new Exception('代码包超过大小限制', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85045){
            throw new Exception('ext_json有不存在的路径', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85046){
            throw new Exception('tabBar中缺少path', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85047){
            throw new Exception('pages字段为空', 'COMMIT_FAIL');
        }
        if($res['errcode']== 85048){
            throw new Exception('ext_json解析失败', 'COMMIT_FAIL');
        }

        return ['data'=>$res];

        //$res = $miniProgram->code->changeVisitStatus($action);
    }
    /*
     * 2、获取体验小程序的体验二维码
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */
    public function getQrcode()
    {
        $this->validate(null, [
            'appId'=>'required|string',
            'refreshToken'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        /*$authLogic = new OpenAppWechatLogic([
            'wechatAppId'=> $wechat['appId'],
        ]);
        $wechatInfo = $authLogic->getWechatInfo();*/
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['refreshToken']);
        //$miniProgram = $openPlatform->miniProgram('wx5e23cca9723000ea','refreshtoken@@@v8BTKXRxTKp0SSYCMcnS7_TPljlWzCqwb2OGA15a0Uc');
        $res = $miniProgram->code->getQrcode();
        return $res;
    }

    /*
     * 3、获取授权小程序帐号的可选类目
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */

    public function getCategory()
    {
        $this->validate(null, [
            'appId'=>'required|string',
            'refreshToken'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        /*$authLogic = new OpenAppWechatLogic([
            'wechatAppId'=> $wechat['appId'],
        ]);
        $wechatInfo = $authLogic->getWechatInfo();*/
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['refreshToken']);
        //$miniProgram = $openPlatform->miniProgram('wx5e23cca9723000ea','refreshtoken@@@v8BTKXRxTKp0SSYCMcnS7_TPljlWzCqwb2OGA15a0Uc');
        $res = $miniProgram->code->getCategory();
        if($res['errcode'] == -1){
            throw new Exception('系统繁忙', 'SYSTEM_ERROR');
        }
        //$page = $this->getPage($wechat['appId'],$wechatInfo->authorizerRefreshToken);
        $page = $this->getPage($wechat['appId'],$wechat['refreshToken']);
        $arr = array_merge($res,$page);
        return $arr;
    }

    /*
       4、获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
    */
    public function getPage($appId,$refreshToken)
    {
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($appId,$refreshToken);
        $res = $miniProgram->code->getPage();
        if($res['errcode'] == -1){
            throw new Exception('系统繁忙', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86000){
            throw new Exception('不是由第三方代小程序进行调用', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86001){
            throw new Exception('不存在第三方的已经提交的代码', 'SYSTEM_ERROR');
        }
        return $res;
    }

    /*
     * 5、将第三方提交的代码包提交审核（仅供第三方开发者代小程序调用）submit_audit
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * $arr=[
            [
                'address'=>'pages/index/index',
                'tag'=>'首页',
                'first_class'=>'IT科技',
                'second_class'=>'硬件与设备',
                'first_id'=>210,
                'second_id'=>211,
                'third_id'=>212,
                'title'=>'首页'
            ],
            [
                'address'=>'pages/about/about',
                'tag'=>'关于我们',
                'first_class'=>'IT科技',
                'second_class'=>'硬件与设备',
                'first_id'=>210,
                'second_id'=>211,
                'third_id'=>212,
                'title'=>'关于我们'
            ]
        ];
    */

    public function submitAudit()
    {
        //$appId = 'wx5e23cca9723000ea';
        //$refreshToken = 'refreshtoken@@@v8BTKXRxTKp0SSYCMcnS7_TPljlWzCqwb2OGA15a0Uc';

        $this->validate(null, [
            'appId'=>'required|string',
            'refreshToken'=>'required|string',
            'address'=>'required|string',
            'tag'=>'required|string',
            'firstClass'=>'required|string',
            'secondClass'=>'required|string',
            'firstId'=>'required|string',
            'secondId'=>'required|string',
            'thirdId'=>'string',
            'title'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        $appId=$wechat['appId'];
        $refreshToken=$wechat['refreshToken'];
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($appId,$refreshToken);
        $data=[];
        $data=[
            [
                'address'=>$this->verifyData['address'],//'pages/index/index',
                'tag'=>$this->verifyData['tag'],//'首页',
                'first_class'=>$this->verifyData['firstClass'],//'房地产',
                'second_class'=>$this->verifyData['secondClass'],//'装修/建材',
                'first_id'=>$this->verifyData['firstId'],//135,
                'second_id'=>$this->verifyData['secondId'],//146,
                'third_id'=>$this->verifyData['thirdId'],//147,
                'title'=>$this->verifyData['title'],//'首页'
            ],
        ];
        $res = $miniProgram->code->submitAudit($data);
        if($res['errcode'] == -1){
            throw new Exception('系统繁忙', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86000){
            throw new Exception('不是由第三方代小程序进行调用', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86001){
            throw new Exception('不存在第三方的已经提交的代码', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85006){
            throw new Exception('标签格式错误', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85007){
            throw new Exception('页面路径错误', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85008){
            throw new Exception('类目填写错误', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85009){
            throw new Exception('已经有正在审核的版本', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85010){
            throw new Exception('item_list有项目为空', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85011){
            throw new Exception('标题填写错误', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85023){
            throw new Exception('审核列表填写的项目数不在1-5以内', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85077){
            throw new Exception('小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86002){
            throw new Exception('小程序还未设置昵称、头像、简介。请先设置完后再重新提交。', 'SYSTEM_ERROR');
        }
        return $res;
    }

    //6、获取审核结果.当小程序有审核结果后，第三方平台将可以通过开放平台上填写的回调地址，获得审核结果通知。
    /*
     * 审核通过时，接收到的推送XML数据包示例如下：
        <xml><ToUserName><![CDATA[gh_fb9688c2a4b2]]></ToUserName>
        <FromUserName><![CDATA[od1P50M-fNQI5Gcq-trm4a7apsU8]]></FromUserName>
        <CreateTime>1488856741</CreateTime>
        <MsgType><![CDATA[event]]></MsgType>
        <Event><![CDATA[weapp_audit_success]]></Event>
        <SuccTime>1488856741</SuccTime>
        </xml>
    */

    /*
     * 7、查询某个指定版本的审核状态（仅供第三方代小程序调用）
     * status	审核状态，其中0为审核成功，1为审核失败，2为审核中
     * reason	当status=1，审核被拒绝时，返回的拒绝原因
     *  -1	系统繁忙
        86000 	不是由第三方代小程序进行调用
        86001 	 不存在第三方的已经提交的代码
        85012 	 无效的审核id
     * */
    public function getAuditstatus(/*$appId,$refreshToken*/)
    {
        //$auditId='423304996';//桥架
        //$auditId='423044837';//线管
        //$auditId='460073789';//线槽
        $this->validate(null, [
            'auditId'=>'required|string',
            'appId'=>'required|string',
            'refreshtoken'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        $auditId=$wechat['auditId'];
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        //$miniProgram = $openPlatform->miniProgram('wx5e23cca9723000ea','refreshtoken@@@v8BTKXRxTKp0SSYCMcnS7_TPljlWzCqwb2OGA15a0Uc');
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['refreshtoken']);
        $res = $miniProgram->code->getAuditstatus($auditId);
        if($res['errcode'] == -1){
            throw new Exception('系统繁忙', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86000){
            throw new Exception('不是由第三方代小程序进行调用', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 86001){
            throw new Exception('不存在第三方的已经提交的代码', 'SYSTEM_ERROR');
        }
        if($res['errcode'] == 85012){
            throw new Exception('无效的审核id', 'SYSTEM_ERROR');
        }
        return $res;
    }

    /*
     * 8、查询最新一次提交的审核状态（仅供第三方代小程序调用）
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */
    public function getLatestAuditstatus(/*$appId,$refreshToken*/)
    {
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        //$miniProgram = $openPlatform->miniProgram($appId,$refreshToken);
        $miniProgram = $openPlatform->miniProgram('wx5a6251d11316e8cc','refreshtoken@@@FR27JUdYGg1P3RH4Cws8ocZ7UL1IiOZ8Qbss4MHocRw');
        $res = $miniProgram->code->getLatestAuditstatus();
        if($res['errcode'] == -1){
            throw new Exception('系统繁忙', 'SYSTEM_ERROR');
        }
        return $res;
    }

    /*
     * 9、发布已通过审核的小程序（仅供第三方代小程序调用）
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */
    public function release(/*$appId,$refreshToken*/)
    {
        $this->validate(null, [
            'appId'=>'required|string',
            'refreshtoken'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        //$miniProgram = $openPlatform->miniProgram('wx1d8b86b3fd1cdfbd','refreshtoken@@@aZNWx0tm9_x8OkjHHcJZN8jSchvAsGCn_To83lTWKjQ');
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['refreshtoken']);
        $res = $miniProgram->code->release();
        return $res;
    }

    /*
     * 10、修改小程序线上代码的可见状态（仅供第三方代小程序调用）
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     * */
    public function changeVisitstatus($appId,$refreshToken)
    {
        $action='';
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $miniProgram = $openPlatform->miniProgram($appId,$refreshToken);
        $res = $miniProgram->code->changeVisitstatus($action);
        return $res;
    }

    /*
     * 1、设置小程序服务器域名
     * 具体参考微信文档
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489138143_WPbOO&token=&lang=zh_CN
     * */
    public function modifyDomain(/*$appId,$refreshToken*/)
    {
        $this->validate(null, [
            'appId'=>'required|string',
            'accessToken'=>'required|string',
            'url'=>'required|string',
        ]);
        $wechat = $this->verifyData;
        $res = [
            'action'=>'add',
            'requestdomain'=>['https://'.$wechat['url'],'https://'.$wechat['url']],
            'wsrequestdomain'=>['wss://'.$wechat['url'],'wss://'.$wechat['url']],
            'uploaddomain'=>['https://'.$wechat['url'],'https://'.$wechat['url']],
            'downloaddomain'=>['https://'.$wechat['url'],'https://'.$wechat['url']],
        ];
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        //$miniProgram = $openPlatform->miniProgram('wx5e23cca9723000ea','refreshtoken@@@v8BTKXRxTKp0SSYCMcnS7_TPljlWzCqwb2OGA15a0Uc');
        $miniProgram = $openPlatform->miniProgram($wechat['appId'],$wechat['accessToken']);
        $res = $miniProgram->domain->modify($res);
        return $res;
    }

    /*
     *  小程序审核撤回 未完成
     */
    public function withdraw() {
        $this->validate(null, [
            'wechatAppId'=>'',
        ]);
        $wechatAppId = $this->verifyData['wechatAppId']; // 小程序 id

        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        $openAddWechatModel = new OpenAppWechatModel();
        $refreshToken =  $openAddWechatModel->where('wechat_app_id', $wechatAppId)->firstHumpArray(['authorizer_refresh_token']);
        $miniProgram = $openPlatform->miniProgram($wechatAppId, $refreshToken['authorizerRefreshToken']);

        $res = $miniProgram->code->withdrawAudit();

        return $res;

    }

}


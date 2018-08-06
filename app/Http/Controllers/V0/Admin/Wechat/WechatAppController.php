<?php

namespace App\Http\Controllers\V0\Admin\Wechat;

use App\Logic\Exception;
use App\Logic\V0\Wechat\AppletTemplateLogic;
use App\Logic\V0\Wechat\WechatAppLogic;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;
use EasyWeChat\Factory;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use JiaLeo\Laravel\Wechat\Wechat;
use JiaLeo\Laravel\Wechat\WechatOrigin;

class WechatAppController extends Controller
{
    public $component_verify_ticket;

    /**
     * 添加小程序
     * */
    public function add()
    {
        $this->verify(
            [
                'wechatAppId'=>'',
                'appName' =>'',
                'isServer' =>'no_required',
                'isAuth' =>'no_required',
            ]
            , 'POST');
        WechatAppLogic::add($this->verifyData);
        return;
    }
    /**
     * 获取全部列表
     * */
    public function getLists(){
        $this->verify(
            [
                'wechatAppId' => 'no_required',//小程序ID
                'appName' => 'no_required',//小程序名称
                'isRelease' => 'no_required',//发布状态
            ]
            , 'GET');
        $res = WechatAppLogic::getList($this->verifyData);
        return $res;
    }
    /**
     * 获取单条
     * */
    public function getOne(){
        $this->verify(
            [
                'appletsId' => '',
            ]
            , 'GET');
        $res = WechatAppLogic::getOne($this->verifyData['appletsId']);
        return ['data'=>$res];
    }
    /**
     * 修改
     * */
    public function edit()
    {
        $this->verify(
            [
                'appletsId'=>'',
                'wechatAppId'=>'',
                'appName' =>'',
                'isServer' =>'no_required',
                'isAuth' =>'no_required',
            ]
            , 'POST');
        WechatAppLogic::edit($this->verifyData);
        return;
    }
    /**
     * 删除
     * */
    public function delete(){
        $this->verify(
            [
                'appletsId' => '',
            ]
            , 'POST');
        WechatAppLogic::delete($this->verifyData['appletsId']);
        return;
    }
    /**
     * 小程序提交审核
     * 具体看文档
     * */
    public function commitApplets()
    {
        $this->validate(null, [
            'appletsId'=>'required|integer',
            'appletTemplateId'=>'required|integer',
        ]);
        //获取小程序信息
        $applets = WechatAppLogic::getOne($this->verifyData['appletsId']);
        if($applets['isRelease']==1){
            throw new Exception('代码已提交在审核中...', 'SYSTEM_ERROR');
        }
        //获取模板信息
        $template = AppletTemplateLogic::getOne($this->verifyData['appletTemplateId']);
        //上传小程序代码
        $this->commit($applets['wechatAppId'],$template);
        //获取可选类目
        $cate = $this->getCategory($applets['wechatAppId']);
        //获取页面配置
        $page = $this->getPage($applets['wechatAppId']);
        //提交审核
        $submit = $this->submitAudit($applets['wechatAppId'],$cate['category_list'],$page['page_list']);
        //把审核ID添加到数据库
        $review['appletsId']=$applets['appletsId'];
        $review['isRelease']=1;//审核中
        $review['auditid']=$submit['auditid'];
        WechatAppLogic::edit($review);
        //添加小程序版本
//        WechatAppLogic::addAuditid($applets['wechatAppId'],$template,$submit['auditid']);
        return ['data'=>$submit];
    }

    /**
     * 获取RefreshToken
     * */
    public function getRefreshToken($wechatAppId){
        $authLogic = new OpenAppWechatLogic([
            'wechatAppId'=> $wechatAppId,
        ]);
        $wechatInfo = $authLogic->getWechatInfo();
        if(empty($wechatInfo->authorizerRefreshToken)){
            throw new Exception('refreshToken不存在,请检查是否授权', 'NO_REFRESHTOKEN');
        }
        return $wechatInfo;
    }
    /**
     * 配置文件
     * */
    public function getConfig()
    {
        $config = config('wechat.open');
        $openPlatform = Factory::openPlatform($config);
        return $openPlatform;
    }
    /**
     * 长传文件
     * */
    public function commit($wechatAppId,$template)
    {
        $wechatInfo = $this->getRefreshToken($wechatAppId);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($wechatAppId,$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->code->commit($template['templateId'], $template['extJson'], $template['version'], $template['desc']);
        /*$res=[
            'errcode'=>85043,
            'errmsg'=>'ok',
        ];*/
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
        if($res['errcode'] != 0){
            throw new Exception('上传代码失败', 'COMMIT_FAIL');
        }

        return $res;
    }
    /**
     * 获取体验二维码
     * */
    public function getQrcode()
    {
        $this->validate(null, [
            'appletsId'=>'required|integer',
        ]);
        $applets = WechatAppLogic::getOne($this->verifyData['appletsId']);
        $wechatInfo = $this->getRefreshToken($applets['wechatAppId']);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($applets['wechatAppId'],$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->code->getQrcode();
        return $res;
    }
    /**
     * 3、获取授权小程序帐号的可选类目
     * 具体参考微信文档
     * */
    public function getCategory($wechatAppId)
    {
        $wechatInfo = $this->getRefreshToken($wechatAppId);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($wechatAppId,$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->code->getCategory();
        /*$arr=[
            'first_class'=>'生活服务',
            'second_class'=>'家政',
            'first_id'=>150,
            'second_id'=>157,
        ];
        $res = [
            'errcode'=>0,
            'errmsg'=>'ok',
            'category_list'=>[$arr],
        ];*/
        if($res['errcode'] == -1){
            throw new Exception('获取可选类目失败--系统繁忙', 'SYSTEM_ERROR');
        }
        return $res;
    }
    /**
     * 4、获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
     * 具体参考微信文档
     * */
    public function getPage($wechatAppId)
    {
        $wechatInfo = $this->getRefreshToken($wechatAppId);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($wechatAppId,$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->code->getPage();
        /*$arr=[
            "pages/index/index",
            "pages/list/project/project",
            "pages/list/equipment/equipment",
            "pages/contact/contact",
            "pages/about/about",
            "pages/list/location/location",
            "pages/list/place/place",
            "pages/item/news/news",
            "pages/item/location/location",
            "pages/list/news/news",
            "pages/item/equipment/equipment",
            "pages/item/project/project"
        ];
        $res = [
            'errcode'=>0,
            'errmsg'=>'ok',
            'page_list'=>$arr,
        ];*/
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
    /**
     * 提交代码审核
     * */
    public function submitAudit($wechatAppId,$categoryList,$pageList)
    {
        $wechatInfo = $this->getRefreshToken($wechatAppId);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($wechatAppId,$wechatInfo->authorizerRefreshToken);
        $data=[
            [
                'address'=>$pageList[0],//'pages/index/index',
                'tag'=>'首页',//'首页',
                'first_class'=>$categoryList[0]['first_class'],//'房地产',
                'second_class'=>$categoryList[0]['second_class'],//'装修/建材',
                'first_id'=>$categoryList[0]['first_id'],//135,
                'second_id'=>$categoryList[0]['second_id'],//146,
                'third_id'=>$categoryList[0]['third_id'] ?? '',//147,
                'title'=>'首页',//'首页'
            ],
        ];
        /*$res=[
            'errcode'=>0,
            'errmsg'=>'ok',
            'auditid'=>422379153,
        ];*/
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
    /**
     * 审核状态
     * */
    public function getAuditstatus()
    {
        $this->validate(null, [
            'appletsId'=>'required|integer',
        ]);
        $applets = WechatAppLogic::getOne($this->verifyData['appletsId']);
        $res = [];
        if($applets['isRelease'] == -1){
            $res = $this->Auditstatus('发布失败');
        }
        if($applets['isRelease'] ==0){
            $res = $this->Auditstatus('还没发布');
        }
        if($applets['isRelease'] ==2){
            $res = $this->Auditstatus('审核通过，请发布');
        }
        if($applets['isRelease'] == 3){
            $res = $this->Auditstatus('发布成功');
        }
        if($applets['isRelease']==1){
            $wechatInfo = $this->getRefreshToken($applets['wechatAppId']);
            $openPlatform = $this->getConfig();
            $miniProgram = $openPlatform->miniProgram($applets['wechatAppId'],$wechatInfo->authorizerRefreshToken);
            $res = $miniProgram->code->getAuditstatus($applets['auditid']);
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
            $res['message']='审核通过，请发布';
            if($res['status']==2){
                $res['message']='已提交审核，一般一个工作日审核完成，请在审核通过后发布';
            }

            if($res['status']==0){
                //把审核ID添加到数据库
                $review['appletsId']=$applets['appletsId'];
                $review['isRelease']=2;//审核通过
                WechatAppLogic::edit($review);
            }
        }
        return ['data'=>$res];
    }
    /**
     * 状态返回
     * */
    public function Auditstatus($data){
        $res=[
            'errcode'=>0,
            'errmsg'=>'ok',
            'status'=>0,
            'message'=>$data
        ];
        return $res;
    }
    /**
     * 发布已通过审核的小程序（仅供第三方代小程序调用）
     * */
    public function release()
    {
        $this->validate(null, [
            'appletsId'=>'required|integer',
        ]);
        $applets = WechatAppLogic::getOne($this->verifyData['appletsId']);
        if($applets['isServer'] == 0){
            throw new Exception('还没设置服务器域名，请设置服务器域名', 'DOMAIN_ERROR');
        }
        $wechatInfo = $this->getRefreshToken($applets['wechatAppId']);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($applets['wechatAppId'],$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->code->release();
        /*$res=[
            'errcode'=>0,
            'errmsg'=>'ok',
        ];*/
        //发布改变状态
        if($res['errcode']==0){
            $release['isRelease']=3;
        }else{
            $release['isRelease']=-1;
        }
        $release['appletsId']=$this->verifyData['appletsId'];
        WechatAppLogic::edit($release);
        return $res;
    }

    /*
     *
     */
    public function authorization() {

        echo 123;
    }



    /**
     * 1、设置小程序服务器域名
     * 具体参考微信文档
     * */
    public function modifyDomain()
    {
        $this->verify(
            [
                'appletsId'=>'',
                'action'=>'',
                'requestdomain'=>'no_required',
                'wsrequestdomain'=>'no_required',
                'uploaddomain'=>'no_required',
                'downloaddomain'=>'no_required',
            ]
            , 'POST');
        $wechat = $this->verifyData;
        $applets = WechatAppLogic::getOne($wechat['appletsId']);
        if($wechat['action'] == 'get'){
            $res = [
                'action'=>$wechat['action'],
            ];
        }else{
            if(empty($wechat['requestdomain']) && empty($wechat['wsrequestdomain']) && empty($wechat['uploaddomain']) && empty($wechat['downloaddomain'])){
                throw new Exception('请输入域名', 'DOMAIN_ERROR');
            }
            $requestdomain = [];
            $wsrequestdomain = [];
            $uploaddomain = [];
            $downloaddomain = [];
            if(!empty($wechat['requestdomain'])){
                //$requestdomain=['https://'.$wechat['requestdomain'],'https://'.$wechat['requestdomain']];
                $requestdomain=$wechat['requestdomain'];
            }
            if(!empty($wechat['wsrequestdomain'])){
                //$wsrequestdomain=['wss://'.$wechat['wsrequestdomain'],'wss://'.$wechat['wsrequestdomain']];
                $wsrequestdomain=$wechat['wsrequestdomain'];
            }
            if(!empty($wechat['uploaddomain'])){
                //$uploaddomain=['https://'.$wechat['uploaddomain'],'https://'.$wechat['uploaddomain']];
                $uploaddomain=$wechat['uploaddomain'];
            }
            if(!empty($wechat['downloaddomain'])){
                //$downloaddomain=['https://'.$wechat['downloaddomain'],'https://'.$wechat['downloaddomain']];
                $downloaddomain=$wechat['downloaddomain'];
            }
            $res = [
                'action'=>$wechat['action'],
                'requestdomain'=>$requestdomain,
                'wsrequestdomain'=>$wsrequestdomain,
                'uploaddomain'=>$uploaddomain,
                'downloaddomain'=>$downloaddomain,
            ];
        }
        $wechatInfo = $this->getRefreshToken($applets['wechatAppId']);
        $openPlatform = $this->getConfig();
        $miniProgram = $openPlatform->miniProgram($applets['wechatAppId'],$wechatInfo->authorizerRefreshToken);
        $res = $miniProgram->domain->modify($res);
        if($res['errcode'] == 0){
            //改变设置服务器的状态
            $server = [
                'appletsId'=>$wechat['appletsId'],
                'isServer'=>1
            ];
            WechatAppLogic::edit($server);
        }
        return ['data'=>$res];
    }
    /**
     * 版本列表
     * */
    public function getAuditidLists()
    {
        $this->verify(
            [
                'wechatAppId'=>'',
            ]
            , 'GET');
        $res = WechatAppLogic::getAuditidLists($this->verifyData['wechatAppId']);
        return $res;
    }

}

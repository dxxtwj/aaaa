<?php
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
use App\Http\Controllers\V3\Api\Web\Open\AuthController;
use App\Logic\V1\Open\Wechat\SmallPogramLogic;
use App\Model\V1\Open\OpenAppWechatModel;
use App\Model\V3\WechatAuthorizer\WechatAuthorizerModel;

/*
 * 微信小程序的控制器
 */
class SmallPogramController extends Controller
{



//---------------------------------小程序基本信息设置-----------------------------

    /*
     * 1、获取帐号基本信息
     */
    public function getUserInfo() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);

        $res = SmallPogramLogic::getUserInfo($this->verifyData);
        return $res;
    }

    /*
     * 2 小程序名称设置及改名
     */
    public function editUserInfo() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);

        $res = SmallPogramLogic::editUserInfo($this->verifyData);
        return $res;
    }





// -------------------------------------成员管理-----------------------

    /*
     * 1、绑定微信用户为小程序体验者
     */
    public function binding() {

        $this->validate(null, [
            'wechatid' => 'required',//微信号
            'authorizerAppid' => 'required',//授权方appid

        ]);

        $res = SmallPogramLogic::binding($this->verifyData);
        return $res;
    }


    /*
     * 2、解除绑定小程序的体验者
     */
    public function unsetBinding() {

        $this->validate(null, [
            'wechatid' => 'required',//微信号
            'authorizerAppid' => 'required',//授权方appid

        ]);

        $res = SmallPogramLogic::unsetBinding($this->verifyData);
        return $res;

    }

    /*
     * 3. 获取体验者列表
     */
    public function getBinding() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::getBinding($this->verifyData);
        return $res;
    }


//-----------------------------------------代码管理------------------------

    /*
     * 1、为授权的小程序帐号上传小程序代码
     */
    public function upload() {

        $this->validate(null, [
            'templateId' => '',
//            'extJson' => '',
            'authorizerAppid' => 'required',//授权方appid
//            'itemList' => 'required',//提交审核项的一个列表（至少填写1项，至多填写5项）
            'userVersion' => 'required',//
            'userDesc' => 'required',//
        ]);
        $res = SmallPogramLogic::upload($this->verifyData);
        return $res;

    }

    /*
     * 2、获取体验小程序的体验二维码
     */
    public function getCode() {

        $this->validate(null, [
            'path' => '',//指定体验版二维码跳转到某个具体页面
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::getCode($this->verifyData);
        return $res;
    }

    /*
     * 3、获取授权小程序帐号的可选类目
     */
    public function getCategory() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::getCategory($this->verifyData);
        return $res;
    }

    /*
     * 4、获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
     */
    public function getConfig() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::getConfig($this->verifyData);
        return $res;
    }


    /*
     * 5、将第三方提交的代码包提交审核（仅供第三方开发者代小程序调用）
     */
    public function examine() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
            'itemList' => 'required',//提交审核项的一个列表（至少填写1项，至多填写5项）
        ]);
        $res = SmallPogramLogic::examine($this->verifyData);
        return $res;
    }


    /*
     * 7、查询某个指定版本的审核状态（仅供第三方代小程序调用）
     */
    public function getStatus() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
            'auditid' => 'required',//提交审核时获得的审核id
        ]);
        $res = SmallPogramLogic::getStatus($this->verifyData);
        return $res;
    }

    /*
     * 8、查询最新一次提交的审核状态（仅供第三方代小程序调用）
     */
    public function getNewStatus() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::getNewStatus($this->verifyData);
        return $res;
    }

    /*
     * 9、发布已通过审核的小程序（仅供第三方代小程序调用）
     */
    public function release() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::release($this->verifyData);
        return $res;

    }

    /*
     * 10、修改小程序线上代码的可见状态（仅供第三方代小程序调用）
     */
    public function editStatus() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
            'action' => 'required',//设置可访问状态，发布后默认可访问，close为不可见，open为可见
        ]);
        $res = SmallPogramLogic::editStatus($this->verifyData);
        return $res;

    }

    /*
     * 11. 小程序版本回退（仅供第三方代小程序调用）
     */
    public function withdraw() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::withdraw($this->verifyData);
        return $res;
    }

    /*
     * 12. 查询当前设置的最低基础库版本及各版本用户占比 （仅供第三方代小程序调用）
     */
    public function proportion() {


        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::proportion($this->verifyData);
        return $res;
    }

    /*
     * 15. 小程序审核撤回
     */
    public function smallProgramWithdraw() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
        ]);
        $res = SmallPogramLogic::smallProgramWithdraw($this->verifyData);
        return $res;

    }

// ---------------------------------------小程序代码模版库管理-----------------

    /*
     * 1、获取草稿箱内的所有临时代码草稿
     */
    public function library() {

        $res = SmallPogramLogic::library();
        return $res;
    }

    /*
     * 2、获取代码模版库中的所有小程序代码模版
     */
    public function template() {
//        $this->validate(null, [
//            'authorizerAppid' => 'required',//授权方appid
//        ]);
        $res = SmallPogramLogic::template();
        return $res;
    }

    /*
     * 3、将草稿箱的草稿选为小程序代码模版
     */
    public function setUp() {
        $this->validate(null, [
//            'authorizerAppid' => 'required',//授权方appid
            'draftId' => 'required'//草稿ID，本字段可通过“ 获取草稿箱内的所有临时代码草稿 ”接口获得

        ]);
        $res = SmallPogramLogic::setUp($this->verifyData);
        return $res;
    }

    /*
     * 4、删除指定小程序代码模版
     */
    public function deleteTemplate() {

        $this->validate(null, [
//            'authorizerAppid' => 'required',//授权方appid
            'templateId' => 'required'//草稿ID，本字段可通过“ 获取草稿箱内的所有临时代码草稿 ”接口获得

        ]);
        $res = SmallPogramLogic::deleteTemplate($this->verifyData);
        return $res;
    }


// ----------------------------------微信开放平台账号管理------------------

    /*
     * 1 、 创建 开放平台帐号并绑定公众号/小程序
     */
    public function addPlatform() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid

        ]);
        $res = SmallPogramLogic::addPlatform($this->verifyData);
        return $res;
    }

    /*
     * 2 、 将 公众号/小程序绑定到开放平台帐号下
     */
    public function bindingSmallProgram() {


        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid

        ]);
        $res = SmallPogramLogic::bindingSmallProgram($this->verifyData);
        return $res;
    }



// ----------------------------------  第三方平台的权限

    /*
     * 第三方平台可以使用接口拉取当前所有已授权的帐号基本信息。
     */
    public function getJurisdiction() {

        $this->validate(null, [
            'offset' => '',//	偏移位置/起始位置
            'count' => '',//拉取数量，最大为500

        ]);
        $res = SmallPogramLogic::getJurisdiction($this->verifyData);
        return $res;
    }






// ---------------------------------- 修改服务器地址------------------------------------

    /*
     * 1、设置小程序服务器域名
     */
    public  function addServerDomain() {
        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
            'action' => 'required',//add添加, delete删除, set覆盖, get获取。当参数是get时不需要填四个域名字段
            'requestdomain' => '',//request合法域名，当action参数是get时不需要此字段
            'wsrequestdomain' => '',//socket合法域名，当action参数是get时不需要此字段
            'uploaddomain' => '',//uploadFile合法域名，当action参数是get时不需要此字段
            'downloaddomain' => '',//downloadFile合法域名，当action参数是get时不需要此字段
        ]);
        $res = SmallPogramLogic::addServerDomain($this->verifyData);
        return $res;
    }


    /*
     * 2、设置小程序业务域名（仅供第三方代小程序调用）
     */
    public function addBusinessDomain() {

        $this->validate(null, [
            'authorizerAppid' => 'required',//授权方appid
            'action' => '',//add添加, delete删除, set覆盖, get获取。当参数是get时不需要填webviewdomain字段。如果没有action字段参数，则默认将开放平台第三方登记的小程序业务域名全部添加到授权的小程序中
            'webviewdomain' => '',//小程序业务域名，当action参数是get时不需要此字段

        ]);
        $res = SmallPogramLogic::addBusinessDomain($this->verifyData);
        return $res;
    }





// ----------------------------------自己写的API，不是微信那边的-------------------

    /*
     * 获取小程序列表
     */
    public function getSmallProgram() {

        $this->validate(null, [
            'authorizerAppid' => '',//授权方appid

        ]);
        $res = SmallPogramLogic::getSmallProgram($this->verifyData);
        return $res;

    }


    /*
     * 删除小程序
     */
    public function deleteSmallProgram() {

        $this->validate(null, [
            'authorizerAppid' => '',//授权方appid

        ]);
        SmallPogramLogic::deleteSmallProgram($this->verifyData);
    }



    /*
     * 添加数据库   迁移数据用到的
     */
    public function aa() {


        $this->validate(null, [
            'authorizerAppid' => '',//授权方appid
            'path' => '',//授权方appid

        ]);
        SmallPogramLogic::aa($this->verifyData);

    }

}


<?php

namespace App\Http\Controllers\Api;

use App\Logic\Message\MessageLogic;
use App\Logic\Message\MessageCategoryLogic;
use App\Logic\V2\User\LoginLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Http\Middleware\SiteId;
use \App\Http\Controllers\Controller;

class MessageController extends Controller
{

    //添加留言
    public function AddMessage()
    {
        $this->verify(
            [
                'messageCateId' => 'no_required',//分类
                'productId' => 'no_required',//产品ID
                'newsId' => 'no_required',//产品ID
                'messagePerson' => 'no_required',//名称
                'sex' => 'no_required',//性别
                'declare' => 'no_required',//申报主体
                'declareType' => 'no_required',//申报主体-单位、个人
                'declareCategory' => 'no_required',//申报类别:1私人会所类、2民居类、3别墅花园类
                'position' => 'no_required',//职位
                'messagePhone' => 'no_required',//手机
                'messageEmial' => 'no_required',//邮件
                'messageCompany' => 'no_required',//公司
                'messageAddress' => 'no_required',//地址
                'messageContent' => 'no_required',//内容
                'messageTime' => 'no_required',//时间
                'messageCourse' => 'no_required',//课程
            ]
            , 'POST');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        MessageLogic::addAll($this->verifyData);
    }

    //登录留言
    public function addMessageByUid()
    {
        $this->verify(
            [
                'messageCateId' => 'no_required',//分类
                'messagePerson' => 'no_required',//名称
                'sex' => 'no_required',//性别
                'declare' => 'no_required',//申报主体
                'declareType' => 'no_required',//申报主体-单位、个人
                'declareCategory' => 'no_required',//申报类别:1私人会所类、2民居类、3别墅花园类
                'position' => 'no_required',//职位
                'messagePhone' => 'no_required',//手机
                'messageEmial' => 'no_required',//邮件
                'messageCompany' => 'no_required',//公司
                'messageAddress' => 'no_required',//地址
                'messageContent' => 'no_required',//内容
            ]
            , 'POST');
        //是否登录
        /*$uid = LoginLogic::isLogin();
        if(empty($uid)){
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }*/
        $uid=1;
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        $this->verifyData['uid']=$uid;
        MessageLogic::addAll($this->verifyData);
    }

    //获取分类列表
    public function getMessageCateLists()
    {
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = MessageCategoryLogic::getMessageCateLists($data);
        return ['lists'=>$res];
    }

    //获取留言列表
    public function getMessageLists()
    {
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = MessageLogic::getMessageLists($data);
        return $res;
    }

    //添加留言--要登录
    public function AddUserMessage()
    {
        $this->verify(
            [
                'newsId'=>'',
                'messagePerson' => '',//名称
                'messageContent' => 'no_required',//内容
            ]
            , 'POST');
        $languageId = SiteId::getLanguageId();
        $this->verifyData['languageId']=$languageId;
        MessageLogic::addUserMessage($this->verifyData);
    }


}

<?php

namespace App\Http\Controllers\Admin\Message;

use App\Logic\Message\MessageCategoryLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MessageCategoryController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      MessageCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //添加
    public function AddMessageCate()
    {
        $this->verify(
            [
                'isOn'=>'',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
            ]
            , 'POST');
        MessageCategoryLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getMessageCateLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'isOn' => 'no_required',
            ]
            , 'GET');
        $res = MessageCategoryLogic::getMessageCateLists($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getMessageCateOne(){
        $this->verify(
            [
                'messageCateId' => '',
            ]
            , 'GET');
        $res = MessageCategoryLogic::getMessageCateOne($this->verifyData['messageCateId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      MessageCateTitle  分类名称
     *      siteTitle       seo站点标题
     *      siteKeywords    站点关键字
     *      siteDescription 描述
     *  ]
     */
    //修改
    public function editMessageCate(){
        $this->verify(
            [
                'messageCateId' => '',//分类id
                'isOn' => '',//是否显示
                'sort'=>'',//排序
                'lang'=>'',
            ]
            , 'POST');
        MessageCategoryLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteMessageCate(){
        $this->verify(
            [
                'messageCateId' => '',//新闻ID
            ]
            , 'POST');
        MessageCategoryLogic::delAffair($this->verifyData['messageCateId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'messageCateId' => '',
                'isOn' => ''
            ]
            , 'POST');
        MessageCategoryLogic::isShow($this->verifyData,$this->verifyData['messageCateId']);

        return;
    }



}

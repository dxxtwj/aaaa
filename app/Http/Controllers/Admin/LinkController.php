<?php

namespace App\Http\Controllers\Admin;

use App\Logic\LinkLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Http\Middleware\SiteId;
use \App\Http\Controllers\Controller;

class LinkController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      linkTitle       标题--不必填
     *  ]
     */
    //添加
    public function AddLink()
    {
        $this->verify(
            [
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'linkUrl' => 'no_required',//链接--不必填
                'linkImage' => 'no_required',//图片--不必填
                'lang' => '',//
            ]
            , 'POST');
        LinkLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getLinkLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'isOn' => 'no_required',
            ]
            , 'GET');
        $res = LinkLogic::getLinkList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getLinkOne(){
        $this->verify(
            [
                'linkId' => '',
            ]
            , 'GET');
        $res = LinkLogic::getLinkOne($this->verifyData['linkId']);

        return ['data'=>$res];
    }

    /*
     *  lang[
     *      languageId      语言ID
     *      linkTitle       标题--不必填
     *  ]
     */
    //修改
    public function editLink(){
        $this->verify(
            [
                'linkId' => '',//ID
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'linkUrl' => 'no_required',//链接
                'linkImage' => 'no_required',//图片
                'lang' => '',//

            ]
            , 'POST');
        LinkLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteLink(){
        $this->verify(
            [
                'linkId' => '',//新闻ID
            ]
            , 'POST');
        LinkLogic::delAffair($this->verifyData['linkId']);

        return;
    }
    //获取语言ID---测试
    public function getTest(){
        $res = SiteId::getLanguageId();
        return ['data'=>$res];
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'linkId' => '',
                'isOn' => ''
            ]
            , 'POST');
        LinkLogic::isShow($this->verifyData,$this->verifyData['linkId']);

        return;
    }


}

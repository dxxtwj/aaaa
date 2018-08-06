<?php

namespace App\Http\Controllers\Admin\Menu;

use App\Logic\Menu\NavUrlLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use App\Http\Middleware\SiteId;
use \App\Http\Controllers\Controller;

class NavUrlController extends Controller
{
    /*
     *  lang[
     *      languageId      语言ID
     *      NavUrlTitle       标题--不必填
     *  ]
     */
    //添加
    /*public function AddNavUrl()
    {
        $this->verify(
            [
                'sort' => '',//排序
                'isOn' => '',//是否显示
                'navUrl' => '',//链接--不必填
                'lang' => '',//
            ]
            , 'POST');
        NavUrlLogic::addAll($this->verifyData);

    }

    //获取全部列表
    public function getNavUrlLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = NavUrlLogic::getNavUrlList($this->verifyData);

        return ['lists'=>$res];
    }

    //获取单条
    public function getNavUrlOne(){
        $this->verify(
            [
                'navUrlId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = NavUrlLogic::getNavUrl($this->verifyData['navUrlId'],$languageId);

        return ['data'=>$res];
    }*/

    /*
     *  lang[
     *      languageId      语言ID
     *      NavUrlTitle       标题--不必填
     *  ]
     */
    //修改
    /*public function editNavUrl(){
        $this->verify(
            [
                'navUrlId' => '',//ID
                'isOn' => '',//是否显示
                'sort' => '',//排序
                'navUrlUrl' => 'no_required',//链接
                'lang' => '',//

            ]
            , 'POST');
        NavUrlLogic::editAll($this->verifyData);

        return;
    }
    //删除
    public function deleteNavUrl(){
        $this->verify(
            [
                'navUrlId' => '',//新闻ID
            ]
            , 'POST');
        NavUrlLogic::deleteNavUrl($this->verifyData['navUrlId']);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'navUrlId' => '',
                'isOn' => ''
            ]
            , 'POST');
        NavUrlLogic::isShow($this->verifyData,$this->verifyData['navUrlId']);

        return;
    }

    //获取语言ID---测试
    public function getTest(){
        $res = SiteId::getLanguageId();

        return ['data'=>$res];
    }*/



}

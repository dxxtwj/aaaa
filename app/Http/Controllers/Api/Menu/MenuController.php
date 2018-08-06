<?php

namespace App\Http\Controllers\Api\Menu;

use App\Logic\Menu\MenuLogic;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class MenuController extends Controller
{

    //获取全部列表
    public function getMenuLists(){
        $data=[];
        $languageId = SiteId::getLanguageId();
        $data['languageId']=$languageId;
        $data['isOn']=1;
        $res = MenuLogic::getMenuList($data);

        return ['lists'=>$res];
    }

    //获取单条
    public function getMenuOne(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = MenuLogic::getMenuOnes($this->verifyData['menuId'],$languageId);
        return ['data'=>$res];
    }


    //获取父级ID
    public function getParentsId(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getMenuId($this->verifyData['menuId']);
        return ['data'=>$res];
    }

    //获取下一级
    public function getChild(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $res = MenuLogic::getChildId($this->verifyData['menuId']);
        return ['data'=>$res];
    }

    //获取某个类下的所有子类
    public function getMenuKids(){
        $this->verify(
            [
                'menuId' => '',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = MenuLogic::getMenuKids($this->verifyData['menuId'],$languageId);
        return ['lists'=>$res];
    }

    //获取导航名称--url的形式->/lists/news、cases、product/1、2、3...
    public function getMenuName()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = MenuLogic::getMenuIdByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
        return ['data'=>$res];
    }

    public function getMenuNameByUrl()
    {
        $this->verify(
            [
                'urlName' => 'no_required',
                'tableId' => 'no_required',
            ]
            , 'GET');
        $languageId = SiteId::getLanguageId();
        $res = MenuLogic::getMenuNameByUrl($this->verifyData['urlName'],$this->verifyData['tableId'],$languageId);
        return ['data'=>$res];
    }

}

<?php

namespace App\Http\Controllers\Admin\About;

use App\Logic\About\SidebarLogic;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \App\Http\Controllers\Controller;

class SidebarsController extends Controller
{
    //添加
    public function AddSidebar()
    {
        $this->verify(
            [
                'aboutId' =>'',
                'sort' =>'',
                'group'=>'no_required',
                'isOn' =>'',
            ]
            , 'POST');
        SidebarLogic::addSidebar($this->verifyData);
    }

    //获取全部列表
    public function getSidebarLists(){
        $this->verify(
            [
                'languageId' => 'no_required',
                'isOn' => 'no_required',
                'group' => 'no_required',
            ]
            , 'GET');
        $res = SidebarLogic::getSidebarList($this->verifyData);
        return ['lists'=>$res];
    }

    //获取单条
    public function getSidebarOne(){
        $this->verify(
            [
                'sidebarId' => '',
                'languageId' => 'no_required',
            ]
            , 'GET');
        $res = SidebarLogic::getSidebarOne($this->verifyData['sidebarId'],$this->verifyData);
        return ['data'=>$res];
    }

    //修改
    public function editSidebar(){
        $this->verify(
            [
                'sidebarId' => '',//ID
                'aboutId'=>'',
                'group'=>'no_required',
                'isOn' =>'',
                'sort' =>'',
            ]
            , 'POST');
        SidebarLogic::editSidebar($this->verifyData);

        return;
    }

    //是否显示
    public function isShow(){
        $this->verify(
            [
                'sidebarId' => '',
                'isOn' => ''
            ]
            , 'POST');
        SidebarLogic::isShow($this->verifyData,$this->verifyData['sidebarId']);
        return;
    }

    //删除
    public function deleteSidebar(){
        $this->verify(
            [
                'sidebarId' => '',//新闻ID
            ]
            , 'POST');
        SidebarLogic::deleteSidebar($this->verifyData['sidebarId']);

        return;
    }



}

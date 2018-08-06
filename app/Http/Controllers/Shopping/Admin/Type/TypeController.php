<?php

namespace App\Http\Controllers\Shopping\Admin\Type;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Type\TypeLogic;

class TypeController extends Controller
{
    /*
     * 添加商品一级分类
     * @return null
     */
    public function addType() {

        $this->verify(
            [
                'typeName' => '',//分类名字
                'shopId' => 'no_required',//店铺ID, 会是一个数组
                'typeImg' => 'no_required',//分类图片
                'typeOrder' => 'no_required',//排序
                'typeIsEnable' => 'no_required',//启用 0 否 1 是
                'typeIsNavigation' => 'no_required',//是否导航显示 0 否 1 是
                'typeIsSpecial' => 'no_required',//是否特殊分类   1 不是  2 是
                'typePrice' => 'no_required',//减
            ]
            , 'POST');
        TypeLogic::addType($this->verifyData);
        return;
    }

    /*
     * 修改分类
     */
    public function editType() {
        $this->verify(
            [
                'typeId' => '',//分类id
                'typeName' => 'no_required',//分类名字
                'typeImg' => 'no_required',//分类图片
                'typeOrder' => 'no_required',//排序
                'typeIsEnable' => 'no_required',//启用 0 否 1 是
                'typeIsNavigation' => 'no_required',//是否导航显示 0 否 1 是
                'shopId' => 'no_required',//店铺ID
                'typeIsSpecial' => 'no_required',//是否特殊分类
                'typePrice' => 'no_required',//减
            ]
            , 'POST');
        TypeLogic::editType($this->verifyData);
        return;
    }

    /*
     * 查询分类，不传ID则查全部
     */
    public function showType() {
        $this->verify(
            [
                'typeId' => 'no_required',//分类id
            ]
            , 'POST');
        $res = TypeLogic::showType($this->verifyData);
        return $res;
    }

    /*
     * 删除分类
     */
    public function deleteType() {

        $this->verify(
            [
                'typeId' => '',//分类id
            ]
            , 'POST');
        TypeLogic::deleteType($this->verifyData);
        return;
    }

    /*
     * 设置分类为首页导航显示
     */
    public function isNavigation() {

        $this->verify(
            [
                'typeId' => '',//分类id
            ]
            , 'GET');
        TypeLogic::isNavigation($this->verifyData['typeId']);
        return;
    }

    /*
     * 取消分页导航
     */
    public function cancelNavigation() {

        $this->verify(
            [
                'typeId' => '',//分类id
            ]
            , 'POST');
        TypeLogic::cancelNavigation($this->verifyData['typeId']);
        return;
    }

}

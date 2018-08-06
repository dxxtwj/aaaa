<?php

namespace App\Http\Controllers\Shopping\Admin\Shop;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Shop\ShopLogic;

class ShopController extends Controller
{

     /*
      * 添加商家
      * @return null
      */
    public function addShop() {

        $this->verify(
            [
                'shopName' => '',//商家名字
                'shopAddress' => '',//商家地址
                'shopPhone'=>'',//商家手机
                'center'=>'',//坐标
                'shopTime'=>'',//门店营业时间
                'shopOrder'=>'',//排序
                'shopIsShow'=>'',//是否启用  0 否 1 是
                'shopLoginPhone'=>'',//店铺登录手机号
                'shopPassword' => '',//登录密码
            ]
            , 'POST');
        ShopLogic::addShop($this->verifyData);
        return;

    }

    /*
     * 查询商家店铺列表
     * @param shopId int 如果传的话则查单条，如果不传则查全部
     * @return array 查询单条返回一位数组  查询全部返回二维数组
     */
    public function showShop() {
        $this->verify(
            [
                'shopId' => 'no_required',//商家ID
            ]
            , 'POST');
        $res = ShopLogic::showShop($this->verifyData);
        return $res;
    }

    /*
     * 修改商家店铺
     * @return  null
     */
    public function putShop() {

        $this->verify(
            [
                'shopId' => '',//商家店铺id
                'shopName' => 'no_required',//商家店铺名字
                'shopAddress' => 'no_required',//商家店铺地址
                'shopPhone'=>'no_required',//商家店铺手机
                'shopTime'=>'no_required',//门店营业时间
                'shopOrder'=>'no_required',//排序
                'shopIsShow'=>'no_required',//是否启用  0 否 1 是
                'center'=>'no_required',//坐标 lng经度  lat纬度

            ]
            , 'POST');
        ShopLogic::putShop($this->verifyData);
        return;
    }

    /*
     * 删除商家店铺
     * @return null
     */
    public function deleteShop() {
        $this->verify(
            [
                'shopId' => '',//商家店铺id
            ]
            , 'POST');
        ShopLogic::deleteShop($this->verifyData);
        return;
    }

    /*
     * 商家修改密码
     */
    public function editPassword() {
        $this->verify(
            [
                'shopId' => '',//商家店铺id
                'shopLoginPhone' => '',//商家店铺id
                'shopPassword' => '',//商家店铺id
            ]
            , 'POST');
        ShopLogic::editPassword($this->verifyData);
        return;
    }

}

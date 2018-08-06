<?php

namespace App\Http\Controllers\Shangrui\Admin\Home;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Admin\Goods\GoodsLogic;
use App\Logic\Shangrui\Admin\Home\HomeLogic;
use App\Model\Shangrui\Home\HomeModel;

class HomeController extends Controller
{
    /*
     * 添加轮播图
     */
    public function addHome(){
        $this->verify(
            [
                'homeImg' => 'no_required',//  图片
                'goodsId' => 'no_required',  // 商品ID
                'homeOrder' => 'no_required', // 推荐排序
                'homeContents' => 'no_required',  //推荐内容
            ]
            ,'POST');
        $res = HomeLogic::addHome($this->verifyData);
        return $res;
    }
    /*
     * 修改轮播图
     */
    public function editHome(){
        $this->verify(
            [
                'homeId' => '',
                'homeImg' => 'no_required',
                'goodsId' => 'no_required',
                'homeOrder' => 'no_required',
                'homeContents' => 'no_required',
            ]
            ,'POST');
        $res = HomeLogic::editHome($this->verifyData);
        return $res;
    }
    /*
     * 查询轮播图
     */
    public function showHome(){
        $this->verify(
            [
                'homeId' =>'no_required',
            ]
            ,'GET');
        $res = HomeLogic::showHome($this->verifyData);
        return $res;
    }

    /*
     *  删除轮播图
     */
    public function deleteHome(){
        $this->verify(
            [
                'homeId' => '',
            ]
            ,'POST');
        $res = HomeLogic::deleteHome($this->verifyData);
        return $res;
    }
}
<?php

namespace App\Http\Controllers\Shopping\Admin\Goods;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\Goods\GoodsLogic;

class GoodsController extends Controller
{

    /*
     * 添加商品
     */
    public function addGoods() {

        $this->verify(
            [
                'goodsName' => '',//商品名字
                'typeId' => '',//关联分类ID
                'goodsPrice'=>'',//商品价格，没有多规格的价格
                'goodsShow'=>'no_required',//是否上架 0：否  1：是
                'goodsContents'=>'no_required',//商品描述
                'goodsOrder'=>'no_required',//  商品排序
                'shopId' => 'no_required',//关联店铺的ID
                'goodsImg' => 'no_required',//商品图片
                'goodsIsRecommend'=>'no_required',//  是否推荐到首页  0 否  1是
                'goodsIntroduce'=>'no_required',//  商品介绍图
                'goodsFormat' => 'no_required',//多规格   会是一个字符串
                'goodsSpecialPrice' => 'no_required',
                'goodsCode' => 'no_required',// 商品编号
                'xuniSellNumber' => 'no_required', //商品销售数量
            ]
            , 'POST');
        $res = GoodsLogic::addGoods($this->verifyData);
        return $res;
    }

    /*
     * 修改商品
     */
    public function editGoods() {
        $this->verify(
            [
                'goodsId' => '',//商品id
                'goodsName' => 'no_required',//商品名字
                'goodsShow'=>'no_required',//是否上架 0：否  1：是
                'goodsContents'=>'no_required',//商品描述
                'goodsPrice'=>'no_required',//商品价格，没有多规格的价格
                'goodsOrder'=>'no_required',//  商品排序
                'shopId' => 'no_required',//关联店铺的ID
                'typeId' => 'no_required',//关联分类ID
                'goodsImg' => 'no_required',//商品图片
                'goodsIsRecommend'=>'no_required',//  是否推荐到首页  0 否  1是
                'goodsIntroduce'=>'no_required',//  商品介绍图
                'goodsFormat' => 'no_required',//多规格   会是一个字符串
                'goodsSpecialPrice' => 'no_required',
                'goodsCode' => 'no_required',// 商品编号
                'xuniSellNumber' => 'no_required', //虚拟销量
            ]
            , 'POST');
        $res = GoodsLogic::editGoods($this->verifyData);
        return $res;
    }

    /*
     * 查询商品表
     * 传商品ID则查单条，不传则查全部
     */
    public function showGoods() {
        $this->verify(
            [
                'goodsId' => 'no_required',//商品id
                'goodsName' => 'no_required',
                'typeId' => 'no_required',
                'shopId' => 'no_required',
                'goodsCode' => 'no_required',//商品编号
            ]
            , 'GET');
        $res = GoodsLogic::showGoods($this->verifyData);
        return $res;
    }

    /*
     * 删除商品
     */
    public function deleteGoods() {
        $this->verify(
            [
                'goodsId' => '',//商品id
            ]
            , 'POST');
        $res = GoodsLogic::deleteGoods($this->verifyData['goodsId']);
        return $res;
    }
    /*
     * 修改销量
     */
    public function editSell()
    {
        $this->verify(
        [
            'goodsId' => '',//商品id
            'xuniSellNumber' => 'no_required', //虚拟销量
        ]
        ,'POST');

        $res = GoodsLogic::editSell($this->verifyData);
        return $res;
    }

}

<?php

namespace App\Http\Controllers\Shangrui\Admin\Goods;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Admin\Goods\GoodsLogic;

class GoodsController extends Controller
{

    /*
     * 添加商品
     */
    public function addGoods(){

        $this->verify(
            [
                'goodsName'=>'', //商品名称
                'goodsImg'=>'no_required',//商品图片
                'goodsCode'=>'no_required',//商品编号
                'goodsContents'=>'no_required',//商品描述
                'goodsStatus'=>'no_required', //是否推荐到首页
                'goodsIsRecommend'=>'no_required', //是否推荐到首页  0否  1 是
                'goodsIsNew' => 'no_required', //是否为新品推荐 0否 1是
                'typeId'=>'', //关联分类ID
                'goodsPrice'=>'',//商品价格
                'goodsIntroduce'=>'no_required',//  商品介绍图
                'goodsFormat' => 'no_required',//多规格   会是一个字符串
                'goodsOrder'=>'no_required', //商品排序
                'created_at'=>'no_required', //创建时间
                'updated_at'=>'no_required', //修改时间
            ]
            ,'POST');
        $res = GoodsLogic::addGoods($this->verifyData);
        return $res;
    }

    /*
     * 修改商品
     */
    public function editGoods(){
        $this->verify(
            [
                'goodsId' => '', //商品ID
                'goodsName' => 'no_required',//商品名字
                'typeId' => 'no_required',//关联分类ID
                'goodsPrice'=>'no_required',//商品价格，没有多规格的价格
                'goodsStatus'=>'no_required',//是否上架 0：否  1：是
                'goodsContents'=>'no_required',//商品描述
                'goodsOrder'=>'no_required',//  商品排序
                'shopId' => 'no_required',//关联店铺的ID
                'goodsImg' => 'no_required',//商品图片
                'goodsIsRecommend'=>'no_required',//  是否推荐到首页  0 否  1是
                'goodsIsNew' => 'no_required', //是否为新品推荐 0否 1是
                'goodsIntroduce'=>'no_required',//  商品介绍图
                'goodsFormat' => 'no_required',//多规格   会是一个字符串
                'goodsSpecialPrice' => 'no_required',
                'goodsCode' => 'no_required',// 商品编号
                'xuniSellNumber' => 'no_required', //商品销售数量
            ]
            ,'POST');
        $res = GoodsLogic::editGoods($this->verifyData);
        return $res;
    }

    /*
     * 查询商品表
     * 传商品ID则查单条，不传则查全部
     */
    public function showGoods(){
        $this->verify(
            [
                'goodsId' => 'no_required',
                'goodsName' => 'no_required',
                'goodsCode' => 'no_required',
                'typeId' => 'no_required',
                'goodsIsNew' => 'no_required',
                'goodsIsRecommend' => 'no_required',
            ]
            ,'GET');
        $res = GoodsLogic::showGoods($this->verifyData);
        return $res;
    }

    /*
     * 删除商品
     */
    public function deleteGoods(){
        $this->verify(
            [
                'goodsId' => '', //商品id
            ]
            ,'POST');
        $res = GoodsLogic::deleteGoods($this->verifyData);
        return $res;
    }

}

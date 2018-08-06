<?php

namespace App\Http\Controllers\Shopping\Api\Goods;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Goods\GoodsLogic;

class GoodsController extends Controller
{
    /*
     * 前台显示商品
     */
    public function showGoods() {
        $this->verify(
            [
                'goodsName' => 'no_required', //商品名称
                'goodsId' => 'no_required',//商品Id
                'shopId' => '',//门店ID
            ]
            , 'POST');
        $res = GoodsLogic::showGoods($this->verifyData);
        return $res;
    }


}

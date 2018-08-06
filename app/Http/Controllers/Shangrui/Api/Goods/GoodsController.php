<?php

namespace App\Http\Controllers\Shangrui\Api\Goods;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Goods\GoodsLogic;

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
            ]
            , 'GET');
        $res = GoodsLogic::showGoods($this->verifyData);
        return $res;
    }


}

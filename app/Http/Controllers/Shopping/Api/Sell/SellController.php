<?php

namespace App\Http\Controllers\Shopping\Api\Sell;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Goods\GoodsLogic;

class SellController extends Controller
{
    public function showSell()
    {
        /*
         * 前台查询销量
         */
        $this->verify(
            [
                'goodsId' => '',  //商品id
                'createdAt' => '',  //时间区间
                'nian' => 'no_required',
                'time' => 'no_required',
            ]
            ,'GET');
        $res = GoodsLogic::showSell($this->verifyData);
        return $res;
    }
}

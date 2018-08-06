<?php

namespace App\Http\Controllers\Shopping\Api\Collection;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Collection\CollectionLogic;

class CollectionController extends Controller
{
    /*
     * 添加收藏
     */
    public function addCollection() {
        $this->verify(
            [
                'goodsId' => '',//商品Id
                'shopId' => '',//商家Id
            ]
            , 'POST');
        $res = CollectionLogic::addCollection($this->verifyData);
        return $res;
    }


    /*
     * 取消收藏
     */
    public function deleteCollection() {
        $this->verify(
            [
                'goodsId' => '',//收藏表自增ID，array
            ]
            , 'POST');
        $res = CollectionLogic::deleteCollection($this->verifyData);
        return $res;
    }

    /*
     * 查询收藏
     */
    public function showCollection() {
        $this->verify(
            [
                'collectionId' => 'no_required',//收藏表自增ID
            ]
            , 'POST');
        $res = CollectionLogic::showCollection($this->verifyData);
        return $res;
    }


}

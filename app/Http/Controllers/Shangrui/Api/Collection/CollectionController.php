<?php

namespace App\Http\Controllers\Shangrui\Api\Collection;

use \App\Http\Controllers\Controller;
use App\Logic\Shangrui\Api\Collection\CollectionLogic;

class CollectionController extends Controller
{
    /*
     * 前台用户添加商品收藏
     */
    public function addCollection(){
        $this->verify(
            [
                'goodsId' => '',
            ]
            ,'POST');
        $res = CollectionLogic::addCollection($this->verifyData);
        return $res;
    }
    /*
     *  前台用户取消收藏
     */
    public function deleteCollection(){
        $this->verify(
            [
                'goodsId' => '',
            ]
            ,'POST');
        $res = CollectionLogic::deleteCollection($this->verifyData);
        return $res;
    }
    /*
     *  查询收藏
     */
    public function showCollection(){
        $this->verify(
            [
                'collectionId' => 'no_required',
            ]
            ,'GET');
        $res = CollectionLogic::showCollection($this->verifyData);
        return $res;
    }
}
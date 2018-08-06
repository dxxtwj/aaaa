<?php

namespace App\Http\Controllers\Shopping\Api\Type;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Api\Type\TypeLogic;

class TypeController extends Controller
{


    /*
     * 前台点击分类显示商品
     */
    public function showType() {

        $this->verify(
            [
                'shopId' => '',//店铺ID
                'typeId' => 'no_required',//分类ID
            ]
            , 'GET');
        $res = new TypeLogic();
        $a = $res->showType($this->verifyData);

        return $a;
    }

}

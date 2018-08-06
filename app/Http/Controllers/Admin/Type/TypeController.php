<?php

namespace App\Http\Controllers\Admin\Type;

use \App\Http\Controllers\Controller;
use App\Logic\Type\TypeLogic;

class TypeController extends Controller
{
    /*
     * 添加商品一级分类
     * @return null
     */
    public function addType() {

        $this->verify(
            [
                'typeName' => '',//分类名字
                'isPid' => '',//分类名字
                'typeIsShow' => 'no_required',//是否显示 0 否 1 是
                'typeImg' => '',//分类图片
                'typeSort' => 'no_required',//排序
            ]
            , 'POST');
        TypeLogic::addType($this->verifyData);
        return;
    }

}

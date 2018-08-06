<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\Type;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Type\TypeModel;

class TypeLogic extends ShoppingLogic
{
    public static function showType($data = array()){
        $goodsModel = new GoodsModel();
        $res = $goodsModel
            ->where('type_id', $data['typeId'])
            ->where('goods_status',1)
            ->getDdvPageHumpArray();
        return $res;
    }
}

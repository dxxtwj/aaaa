<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\Goods;


use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;



class GoodsLogic extends ShoppingLogic
{
    //前台显示商品数据
    public static function showGoods($data = array()){
        $goodsModel = new GoodsModel();
        if (empty($data['goodsId'])){
            if (!empty($data['goodsName'])) {
                $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
            }
            $res = $goodsModel
                ->where('goods_status',1)
                ->orderBy('app_goods.goods_order', 'DESC')
                ->getDdvPageHumpArray();
            if (!empty($res['lists'])) {

                foreach ($res['lists'] as $k => $v) {
                    $res['lists'][$k]['goodsIntroduce'] = empty($v['goodsIntroduce']) ? array() : json_decode($v['goodsIntroduce'], true);//其他图片

                }
            }
            return $res;
        } elseif (!empty($data['goodsId'])){

            $goodsWhere['goods_id'] = $data['goodsId'];

            $res = $goodsModel->where($goodsWhere)->firstHumpArray();
            if (!empty($res)){
                $res['goodsIntroduce'] = empty($res['goodsIntroduce']) ? array() : json_decode($res['goodsIntroduce'], true);//商品介绍图
            }
            return ['data' => $res];
        }
    }
}
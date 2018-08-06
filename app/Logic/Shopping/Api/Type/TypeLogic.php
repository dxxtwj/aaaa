<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Type;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Type\TypeModel;

class TypeLogic extends ShoppingLogic
{

    /*
     * 前台点击分类显示商品
     * @param array $data 数据
     * @return 返回分类和商品数据
     */
    public function showType($data) {

        $goodsModel = new GoodsModel();

        if (!empty($data['shopId']) && empty($data['typeId'])) {

            $typeModel = new TypeModel();
            $res = $typeModel
                ->getHumpArray();

            if (!empty($res)) {

                $commonModel = new ShoppingLogic();
                $typeData = $commonModel->getType($res, $data['shopId']);//门店下的分类数据
                return ['lists' => $typeData];
            }
        }
        if (!empty($data['typeId'])) {//查询单条，这里的需求只是查询商品数据，没必要查询分类数据

            $goods = $goodsModel
                ->where('type_id', $data['typeId'])
                ->where('goods_show',1)
                ->getHumpArray();

            if (!empty($goods)) {

                $commonModel = new ShoppingLogic();

                $typeData = $commonModel->getType($goods, $data['shopId']);//处理
                $goods = $commonModel->goodsPrice($typeData, $data['shopId']);//处理数据

                foreach ($goods as $k => $v) {
                    $ids[] = $v['goodsId'];
                }
                $goodsData = $goodsModel->whereIn('goods_id', $ids)->getDdvPageHumpArray();
                $goodsData = $commonModel->goodsPrice($goodsData, $data['shopId']);//处理数据
                return $goodsData;
            }
        }
    }
}
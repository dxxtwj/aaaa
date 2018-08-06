<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Api\Collection;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Collection\CollectionModel;
use App\Model\Shangrui\Goods\GoodsModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

class CollectionLogic extends ShoppingLogic
{
    //添加收藏
    public static function addCollection($data = array())
    {
        $goodsModel = new GoodsModel();
        $collectionModel = new CollectionModel();
        // 判断此商品是否已经被收藏
        $goodsWhere['user_id'] = \Session::get('userId');
        $goodsWhere['goods_id'] = $data['goodsId'];
        $goods = $collectionModel->where($goodsWhere)->firstHumpArray();
        if (!empty($goods)) {
            throw new RJsonError('该商品已经被收藏了', 'COLLECTION_ERROR');
        }
        //如果没被收藏 就存入收藏表
        $collectionModel->setDataByHumpArray($goodsWhere)->save();
    }

    //取消收藏
    public static function deleteCollection($data)
    {
        $collectionModel = new CollectionModel();
        $where['user_id'] = \Session::get('userId');
        $bool = $collectionModel->whereIn('goods_id', $data['goodsId'])->where($where)->delete();
        if (empty($bool)) {

            throw new RJsonError('取消失败', 'COLLECTION_ERROR');
        }
    }

    //查询收藏
    public static function showCollection($data, $userId = "")
    {
        if (empty($data['collectionId'])) {
            $collectionModel = new CollectionModel();
            $collectionData = array();
            $goodsData = array();
            $Where['user_id'] = empty($data['userId']) ? \Session::get('userId') : $data['userId'];
            $array = $collectionModel->where($Where)->select(['goods_id', 'collection_id'])->getHumpArray();
//            var_dump($array);die;
            if (!empty($array)) {
                foreach ($array as $k => $v) {
                    $goodsModel = new GoodsModel();
                    $goodsData[] = $goodsModel->where('goods_id', $v['goodsId'])->firstHumpArray();
                }

                foreach ($goodsData as $k1 => $v1) {

                    if ($v1 == null) {

                        unset($goodsData[$k1]);

                    }
                }
                foreach ($goodsData as  $k2 =>$v2) {
                    if ($v2['goodsStatus'] == 1) {
                        $collectionData['shangJia'][] = $goodsData[$k2];

                    } elseif ($v2['goodsStatus'] == 0) {//未上架
                        $collectionData['xiaJia'][] = $goodsData[$k2];

                    }
                }
//                var_dump($collectionData['shangJia']);die;
                if (!empty($collectionData['shangJia'])) {

                    foreach ($collectionData['shangJia'] as $k => $v) {
                        $collectionData['shangJia'][$k]['goodsIntroduce'] = empty($v['goodsIntroduce']) ? array() : json_decode($v['goodsIntroduce'], true);//其他图片

                    }
                }
                if (!empty($collectionData['xiaJia'])) {

                    foreach ($collectionData['xiaJia'] as $k => $v) {
                        $collectionData['xiaJia'][$k]['goodsIntroduce'] = empty($v['goodsIntroduce']) ? array() : json_decode($v['goodsIntroduce'], true);//其他图片

                    }
                }
                return ['lists' =>$collectionData];
            }

        } elseif (!empty($data['collectionId'])) {
            $collectionModel = new CollectionModel();
            $goodsModel = new GoodsModel();

            $where['collection_id'] = $data['collectionId'];
            $array = $collectionModel->select(['goods_id','collection_id'])->where($where)->firstHumpArray();
            if (!empty($array)) {
                $goodsData = $goodsModel->where('goods_id', $array['goodsId'])->firstHumpArray();
                if (!empty($goodsData)){
                    $goodsData['goodsIntroduce'] = empty($goodsData['goodsIntroduce']) ? array() : json_decode($goodsData['goodsIntroduce'], true);//商品介绍图
                }
                return ['data' =>$goodsData];
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Collection;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Collection\CollectionModel;
use App\Model\Shopping\Goods\GoodsModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

class CollectionLogic extends ShoppingLogic
{


    /*
     * 添加收藏
     */
    public static function addCollection($data) {

        $collectionModel = new CollectionModel();
        $collectionData['shop_id'] = $data['shopId'];
        $collectionData['user_id'] = \Session::get('userId');
        $collectionData['goods_id'] = $data['goodsId'];
        $isCollection = $collectionModel->where($collectionData)->firstHumpArray();
        if (!empty($isCollection)) {
            throw new RJsonError('小亲亲~已经存在这个商品的收藏了哦~','COLLECTION_ERROR');
        }
        $collectionModel->setDataByHumpArray($collectionData)->save();
    }


    /*
     * 取消收藏
     */
    public static function deleteCollection($data) {
        $collectionModel = new CollectionModel();
        $where['user_id'] = \Session::get('userId');
        $bool  = $collectionModel->whereIn('goods_id',$data['goodsId'])->where($where)->delete();
        if (empty($bool)) {

            throw new RJsonError('取消失败', 'COLLECTION_ERROR');
        }
    }


    /*
     * 查询收藏
     */
    public static function showCollection($data=array(),$userId='') {

        if (empty($data['collectionId'])) {

            $collectionModel = new CollectionModel();
            $collectionData = array();
            $goodsDatas = array();
            $common = new ShoppingLogic();
            $where['user_id'] = empty($userId) ? \Session::get('userId') : $userId;
            $array = $collectionModel->where($where)->select(['goods_id','collection_id','shop_id'])->getHumpArray();
            if (!empty($array)) {
                foreach ($array as $k => $v) {
                    $goodsModel = new GoodsModel();
                    $goodsData = $goodsModel->where('goods_id', $v['goodsId'])->firstHumpArray();
                    $goodsDatas[] = $common->goodsPrice($goodsData,$array[0]['shopId']);
                }

                foreach ($goodsDatas as $k => $v) {

                    if ($v == null) {

                        unset($goodsDatas[$k]);

                    }
                }
                foreach ($goodsDatas as  $k =>$v) {
                    if ($v['goodsShow'] == 1) {
                        $collectionData['shangJia'][] = $goodsDatas[$k];
                    } elseif ($v['goodsShow'] == 0) {//未上架
                        $collectionData['xiaJia'][] = $goodsDatas[$k];
                    }
                }
            }
            return ['lists' =>$collectionData];

        } elseif (!empty($data['collectionId'])) {
            $collectionModel = new CollectionModel();
            $goodsModel = new GoodsModel();
            $common = new ShoppingLogic();

            $where['collection_id'] = $data['collectionId'];
            $array = $collectionModel->select(['goods_id','collection_id','shop_id'])->where($where)->firstHumpArray();
            if (!empty($array)) {
                $goodsData = $goodsModel->where('goods_id', $array['goodsId'])->firstHumpArray();
                $goodsDatas[] = $common->goodsPrice($goodsData,$array['shopId']);

                return ['data' =>$goodsDatas];
            }
        }
    }
}
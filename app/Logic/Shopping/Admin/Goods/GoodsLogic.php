<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Goods;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Home\HomeModel;
use App\Model\Shopping\Sell\SellModel;
use App\Model\Shopping\Shop\ShopModel;
use App\Model\Shopping\Type\TypeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class GoodsLogic extends ShoppingLogic
{
    /*
     * 商品表添加
     */
    public static function addGoods($data) {

        \DB::beginTransaction();
        try {
            $goodsModel = new GoodsModel();
            $sellModel = new SellModel();

            //设置默认时区
            date_default_timezone_set('PRC');

            $goodsData['goods_name'] = !empty($data['goodsName']) ? $data['goodsName'] : '';
            $goodsData['goods_price'] = !empty($data['goodsPrice']) ? $data['goodsPrice'] : 0;
            $goodsData['type_id'] = !empty($data['typeId']) ? $data['typeId'] : '0';
            $goodsData['shop_id'] = !empty($data['shopId']) ? join(',', $data['shopId']) : '0';
            $goodsData['goods_show'] = empty($data['goodsShow']) ? 0 : 1;
            $goodsData['goods_contents'] = !empty($data['goodsContents']) ? $data['goodsContents'] : '';
            $goodsData['goods_order'] = empty($data['goodsOrder']) ? 0 : $data['goodsOrder'];
            $goodsData['goods_img'] = !empty($data['goodsImg']) ? $data['goodsImg'] : '';
            $goodsData['goods_is_recommend'] = empty($data['goodsIsRecommend']) ? 0 : 1;
            $goodsData['goods_format'] = !empty($data['goodsFormat']) ? $data['goodsFormat'] : '';//是否开启了多规格
            $goodsData['goods_introduce'] = !empty($data['goodsIntroduce']) ? json_encode($data['goodsIntroduce'], true) : '';
            $goodsData['goods_special_price'] = !empty($data['goodsSpecialPrice']) ? json_encode($data['goodsSpecialPrice'], true) : '';
            $goodsData['goods_code'] = !empty($data['goodsCode']) ? $data['goodsCode'] : '';
            $goodsModel->setDataByHumpArray($goodsData)->save();
            $lastId = $goodsModel->getQueueableId();

            if (!empty($data['xuniSellNumber'])) {//   有虚拟销售量
                $sellData['goods_id'] = $lastId;
                $sellData['xuni_sell_number'] = $data['xuniSellNumber'];
                $sellModel->setDataByArray($sellData)->save();
            }

            // 添加销售量表
           // $where['goods_id'] = $data['goodsId'];

//            $common = new ShoppingLogic();
//            $nian = empty($data['nian']) ? date('Y') : $data['nian'];
//            $now = !empty($data['time']) ? $data['time'] : time();
//            $time = $common->timestamp($nian, $now);
//
//            $b['goods_id'] = $data['goodsId'];
//            //原有销售量
//            $sellData = $sellModel->where($where)->whereBetween('created_at',$time)->firstHumpArray();
//            if(!empty($sellData)){
////                $a = $sellData['realSellNumber'];
////                $b['real_sell_number'] = $data['goodsNumber'] + $a;
//                $sellModel->where($where)->updateByHump($b);
//
//            } elseif (empty($sellData)){
////                $b['real_sell_number'] =  $data['goodsNumber'];
//                $b['created_at'] = time();
//                $sellModel->setDataByArray($b)->save();
//            }

            if (!$lastId) {
                throw new RJsonError('添加商品失败', 'SHOP_ERROR');
            }
            \DB::commit();
        }catch(QueryException $e) {

            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'SHOP_ERROR');
        }
    }
    /*
     * 商品表修改
     */
    public static function editGoods($data) {

        \DB::beginTransaction();
        try {

            $goodsModel = new GoodsModel();
            $sellModel = new SellModel();
            $goodsData['goods_price'] = empty($data['goodsPrice']) ? 0 : $data['goodsPrice'];//默认价格
            $goodsData['goods_name'] = !empty($data['goodsName']) ? $data['goodsName'] : '';
            $goodsData['type_id'] = !empty($data['typeId']) ? $data['typeId'] : '0';
            $goodsData['goods_show'] = empty($data['goodsShow']) ? 0 : 1;
            $goodsData['goods_contents'] = !empty($data['goodsContents']) ? $data['goodsContents'] : '';
            $goodsData['goods_order'] = empty($data['goodsOrder']) ? 0 : $data['goodsOrder'];
            $goodsData['goods_img'] = !empty($data['goodsImg']) ? $data['goodsImg'] : '';
            $goodsData['goods_is_recommend'] = empty($data['goodsIsRecommend']) ? 0 : 1;
            $goodsData['goods_format'] = !empty($data['goodsFormat']) ? $data['goodsFormat'] : '';//是否开启了多规格
            $goodsData['goods_introduce'] = !empty($data['goodsIntroduce']) ? json_encode($data['goodsIntroduce'], true) : '';
            $goodsData['shop_id'] = !empty($data['shopId']) ? join(',', $data['shopId']) : '0';
            $goodsData['goods_special_price'] = !empty($data['goodsSpecialPrice']) ? json_encode($data['goodsSpecialPrice'], true) : '';
            $goodsData['goods_code'] = !empty($data['goodsCode']) ? $data['goodsCode'] : '';

            //修改销量操作

            if (!empty($data['xuniSellNumber'])) {

                $sellData['goods_id'] = $data['goodsId'];
                $sellData['xuni_sell_number'] = $data['xuniSellNumber'];
                $sellData['created_at'] = time();
                $datas = $sellModel->where('goods_id',$data['goodsId'])->firstHumpArray();

                if (empty($datas)) {
                   $sellModel->setDataByArray($sellData)->save();
                } elseif (!empty($datas)) {
                    $sellModel->where('goods_id',$data['goodsId'])->updateByHump($sellData);
                }

            }

            $bool = $goodsModel->where('goods_id', $data['goodsId'])->updateByHump($goodsData);

            if (!$bool) {

                throw new RJsonError('修改商品失败', 'SHOP_ERROR');
            }

            \DB::commit();
        } catch(QueryException $e) {
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'EDITGOODS_ERROR');
        }

        return ;
    }
    /*
     * 查询商品表
     * @param string $data['goodsId'] 传则查单条，不传则查所有
     * @param boll $boll 是否查询单条   false为查询单条
     * @return array 一维或二维数组  返回查询成功的数据，内含商家数据  分类数据   商品数据
     */
    public static function showGoods($data=array()) {

        $goodsModel = new GoodsModel();
        $shopModel = new ShopModel();
        $sellModel = new SellModel();

        if (empty($data['goodsId'])) {

            if (!empty($data['goodsName'])) {

                $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
            }

            if (!empty($data['typeId'])) {

                $goodsModel = $goodsModel->where('type_id', $data['typeId']);
            }

            if (!empty($data['goodsCode'])) {

                $goodsModel = $goodsModel->where('goods_code', 'like', '%'.$data['goodsCode'].'%');

            }
            if (!empty($data['shopId'])) {

                $goodsRec1 = $goodsModel
                    ->orderBy('shopping_goods.goods_order', 'DESC')
                    ->whereRaw('FIND_IN_SET(?, shop_id)', $data['shopId'])
                    ->getHumpArray(['goods_id']);

                $goodsModel = new GoodsModel();

                if (!empty($data['goodsName'])) {

                    $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
                }

                if (!empty($data['typeId'])) {

                    $goodsModel = $goodsModel->where('type_id', $data['typeId']);
                }

                if (!empty($data['goodsCode'])) {

                    $goodsModel = $goodsModel->where('goods_code', 'like', '%'.$data['goodsCode'].'%');

                }
                $goodsRec2 = $goodsModel
                    ->where('shop_id', 0)
                    ->getHumpArray(['goods_id']);

                if (!empty($goodsRec1)) {

                    foreach ($goodsRec1 as $k => $v) {
                        $ids[] = $v['goodsId'];
                    }
                }

                if (!empty($goodsRec2)) {

                    foreach ($goodsRec2 as $k => $v) {
                        $ids[] = $v['goodsId'];
                    }
                }
                if (!empty($ids)) {

                    $goodsModel = new GoodsModel();
                    $res = $goodsModel
                        ->orderBy('shopping_goods.goods_order', 'DESC')
                        ->whereIn('goods_id', $ids)
                        ->getDdvPageHumpArray();
                }
            } else {

                $res = $goodsModel
                    ->orderBy('shopping_goods.goods_order', 'DESC')
                    ->getDdvPageHumpArray();
            }

            if (!empty($res['lists'])) {

                foreach ($res['lists'] as $k => $v) {
                    $g = $sellModel->where('goods_id',$v['goodsId'])->firstHumpArray();

                    $arrayShopId = explode(',', $v['shopId']);//分割数组
                    $shopData = $shopModel->whereIn('shop_id', $arrayShopId)->getHumpArray();

                    if (empty($shopData)) {//代表该商品是属于所有店铺
                        $shopData = $shopModel->getHumpArray();
                    }
                    $res['lists'][$k]['shopData'] = empty($shopData) ? array() : $shopData;
                    $res['lists'][$k]['goodsSpecialPrice'] = empty($v['goodsSpecialPrice']) ? array() : json_decode($v['goodsSpecialPrice'],true);
                    $res['lists'][$k]['goodsIntroduce'] = empty($v['goodsIntroduce']) ? array() : json_decode($v['goodsIntroduce'], true);//其他图片
                    $res['lists'][$k]['realSellNumber'] = empty($g['realSellNumber']) ? 0 : $g['xuniSellNumber'];
                    $res['lists'][$k]['xuniSellNumber'] = empty($g['xuniSellNumber']) ? 0 : $g['xuniSellNumber'];
                }

                return $res;
            }

        } elseif (!empty($data['goodsId'])) {//查询单条

            $res = $goodsModel
                ->where('goods_id', $data['goodsId'])
                ->firstHumpArray();

            // 查询销售量表
            $g = $sellModel->where('goods_id',$data['goodsId'])->firstHumpArray();

            if (!empty($res)) {

                $res['realSellNumber'] = empty($g['realSellNumber']) ? 0 : $g['realSellNumber'];
                $res['xuniSellNumber'] = empty($g['xuniSellNumber']) ? 0 : $g['xuniSellNumber'];
                $res['goodsIntroduce'] = empty($res['goodsIntroduce']) ? array() : json_decode($res['goodsIntroduce'], true);//商品介绍图
                $res['goodsSpecialPrice'] = !empty($res['goodsSpecialPrice']) ? json_decode($res['goodsSpecialPrice'], true) : array();//特殊价格


                if (!empty($res['shopId'])) {

                    $arrayWhere = explode(',',$res['shopId']);
                    $shopData = $shopModel->whereIn('shop_id', $arrayWhere)->getHumpArray();//商家

                } else {//为空则说明这个商品属于所有商家

                    $shopData = $shopModel->getHumpArray();
                }
                $res['shopData'] = empty($shopData) ? array() : $shopData;

                return ['data' => $res];
            }
        }
    }

    /*
     * 删除商品
     * @param string $goodsId  商品ID
     * @return null
     */
    public static function deleteGoods($goodsId) {

        $goodsModel = new GoodsModel();
        $homeModel = new HomeModel();

        \DB::beginTransaction();

        try{
            $bool = $homeModel->where('goods_id', $goodsId)->firstHumpArray();

            if (!empty($bool)) {

                throw new RJsonError('首页还有该轮播图或单个商品推荐的显示，删除失败。', 'GOODS_ERROR');
            }

            $arrayWhere['goods_id'] = $goodsId;
            $arrayWhere['goods_is_recommend'] = 1;

            $bool = $goodsModel->where($arrayWhere)->firstHumpArray();

            if (!empty($bool)) {
                throw new RJsonError('首页还有此商品的显示，删除失败', 'GOODS_ERROR');
            }

            $bool = $goodsModel->where('goods_id', $goodsId)->delete();

            if (empty($bool)) {
                throw new RJsonError('删除商品失败', 'GOODS_ERROR');
            }

            // 删除销售量表

            \DB::commit();
        } catch(QueryException $e) {

            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'GOODS_ERROR');
        }
        return ;
    }

}
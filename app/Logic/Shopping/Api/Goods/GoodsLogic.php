<?php
    /**
     * Created by PhpStorm.
     * User: yuelin
     * Date: 2017/6/8
     * Time: 下午2:29
     */

namespace App\Logic\Shopping\Api\Goods;


use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Collection\CollectionModel;
use App\Model\Shopping\Goods\GoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use App\Model\Shopping\Sell\SellModel;


class GoodsLogic extends ShoppingLogic
{

    /*
     * 前台显示商品
     */
    public static function showGoods($data = array())
    {
//        \DB::enableQueryLog();
////        $model = '';
////        $model = (new GoodsModel())->whereHas('goodsPrice', function($req) {
////
//////            $req->where();//goodsModel 的对象
////
////        })->with(['goodsPrice' => function($req){// with 是一对一查询,带条件约束的渴求式加载,渴求式加载多个关联关系
////
//////            $req->where('goods_id', 134);// 这里是goodsPriceModel的对象
////            $req->where('price', 'like', '%22%');
////
////        }])->getHumpArray();
//
//
//        $modes = (new GoodsModel())->whereHas('goodsMasPrice', function($req) {
//
//
//        })->with(['goodsMasPrice' => function($req){
//
//
//
//        }])->with(['goodsPriceAll' => function ($req) {}])->getHumpArray();
//
//        var_dump($modes);
//        var_dump(\DB::getQueryLog());exit;

        $goodsModel = new GoodsModel();
        $sellModel = new SellModel();
        $common = new ShoppingLogic();

        //设置默认时区
        date_default_timezone_set('PRC');

        if (empty($data['goodsId'])) {//列表

            if (!empty($data['goodsName'])) {
                $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
            }
            if (!empty($data['shopId'])) {
                $goodsRec1 = $goodsModel
                    ->orderBy('shopping_goods.goods_order', 'DESC')
                    ->whereRaw('FIND_IN_SET(?, shop_id)', $data['shopId'])
                    ->where('goods_show', 1)
                    ->getHumpArray(['goods_id']);

                $goodsModel = new GoodsModel();

                if (!empty($data['goodsName'])) {
                    $goodsModel = $goodsModel->where('goods_name', 'like', '%'.$data['goodsName'].'%');
                }

                $goodsRec2 = $goodsModel
                    ->where('shop_id', 0)
                    ->where('goods_show', 1)
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
                    $goodsRec = $goodsModel
                        ->orderBy('shopping_goods.goods_order', 'DESC')
                        ->whereIn('goods_id', $ids)
                        ->getDdvPageHumpArray();
                }

            } else {

                $goodsRec = $goodsModel
                    ->where('goods_show', 1)
                    ->orderBy('shopping_goods.goods_order', 'DESC')
                    ->getDdvPageHumpArray();
            }

            if (!empty($goodsRec['lists'])) {

                foreach ($goodsRec['lists'] as $k => $v)
                {
                    $g = $sellModel->where('goods_id',$v['goodsId'])->firstHumpArray();
                    $goodsRec['lists'][$k]['realSellNumber'] = empty($g['realSellNumber']) ? 0 : $g['xuniSellNumber'];
                    $goodsRec['lists'][$k]['xuniSellNumber'] = empty($g['xuniSellNumber']) ? 0 : $g['xuniSellNumber'];
                }
                $res = $common->goodsPrice($goodsRec, $data['shopId']);//处理商品数据
                return $res;
            }

        } elseif (!empty($data['goodsId'])) {//查询单条

            $goodsWhere['goods_id'] = $data['goodsId'];
            $goodsWhere['goods_show'] = 1;//没有下架的
            $sellWhere['goods_id'] = $data['goodsId'];
            $common = new ShoppingLogic();
            $nian = date('Y');
            $now = time();
            $time = $common->timestamp($nian, $now);
            $goodsSell = $sellModel->where($sellWhere)->whereBetween('created_at',$time)->firstHumpArray();

            $res = $goodsModel
                ->where($goodsWhere)
                ->firstHumpArray();

            if(!empty($res)){
                $res['realSellNumber'] = empty($goodsSell['realSellNumber']) ? 0 : $goodsSell['realSellNumber'];
                $res['xuniSellNumber'] = empty($goodsSell['xuniSellNumber']) ? 0 : $goodsSell['xuniSellNumber'];
            } elseif(empty($res)){
                throw new RJsonError('暂无商品数据', 'GOODS_ERROR');
            }

            $userId = \Session::get('userId');
            if (!empty($userId)) {

                $collectionWhere['user_id'] = $userId;
                $collectionWhere['goods_id'] = $res['goodsId'];
                $collectionModel = new CollectionModel();
                $collectionData = $collectionModel->where($collectionWhere)->firstHumpArray();

                if (!empty($collectionData)) {
                    $res['isCollection'] = true;
                } else {
                    $res['isCollection'] = false;
                }
            }
            $common = new ShoppingLogic();
            $res = $common->goodsPrice($res, $data['shopId']);//处理商品数据


            return ['data' => $res];
        }

    }

    /*
     * 前台显示销量
     */
    public static function showSell($data = array())
    {

        date_default_timezone_set('PRC');

        $common = new ShoppingLogic();

        $nian = empty($data['nian']) ? date('Y') : $data['nian'];

        $now = !empty($data['time']) ? $data['time'] : time();

        $time = $common->timestamp($nian, $now);
        $sellmodel = new SellModel();
        if(empty($data['goodsId']))
        {
            throw new RJsonError('缺少参数', 'GOODS_ERROR');
        } elseif(!empty($data['goodsId'])) { //查询单条数据
            $goodsWhere['goods_id'] = $data['goodsId'];
            $goodsWhere['created_at'] = $data['createdAt'];
            $res = $sellmodel->where($goodsWhere)->whereBetween('created_at',$time)->firstHumpArray();
            $res['zxl'] = $res['realSellNumber'] + $res['xuniSellNumber'];// 总销售量
            if (empty($res)){
                throw new RJsonError('暂无商品数据','GOODS_ERROR');
            }

            return ['data' => $res];
        }
    }

}
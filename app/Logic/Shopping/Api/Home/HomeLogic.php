<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Home;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Home\HomeModel;
use App\Model\Shopping\Notice\NoticeModel;
use App\Model\Shopping\Type\TypeModel;

class HomeLogic extends ShoppingLogic
{

    /*
     * 获取首页显示的分类
     */
    public static function homeType($shopId) {

        $typeModel = new TypeModel();
        $homeData=array();
        $typeWhere['type_is_navigation'] = 1;//首页显示
        $homeData1 = $typeModel
            ->where($typeWhere)
//            ->orderBy('type_order', 'DESC')
            ->whereRaw('FIND_IN_SET(?, shop_id)', $shopId)
            ->getHumpArray(['type_id']);

        $homeData2 = $typeModel
            ->where($typeWhere)
            ->where('shop_id', 0)
            ->getHumpArray(['type_id']);

        if (!empty($homeData1)) {

            foreach ($homeData1 as $k => $v) {

                $ids[] = $v['typeId'];

            }
        }

        if (!empty($homeData2)) {

            foreach ($homeData2 as $k => $v) {

                $ids[] = $v['typeId'];
            }
        }

        if (!empty($ids)) {

            $homeData = $typeModel
                ->orderBy('type_order', 'DESC')
                ->whereIn('type_id', $ids)
                ->getHumpArray();
        }


        return ['lists' => $homeData];

    }

    /*
     * 获取首页显示的商品
     */
    public static function homeGoods($shopId) {
        $goodsData=array();
        $goodsModel = new GoodsModel();
        $whereGoods['goods_show'] = 1;//没下架
        $whereGoods['goods_is_recommend'] = 1;//首页显示
        $goodsData1 = $goodsModel//首页显示的商品
            ->where($whereGoods)
            ->whereRaw('FIND_IN_SET(?, shop_id)', $shopId)
            ->getHumpArray();


        $goodsData2 = $goodsModel
            ->where($whereGoods)
            ->where('shop_id',0)
            ->getHumpArray();

        if (!empty($goodsData1)) {

            foreach ($goodsData1 as $k => $v) {

                $ids[] = $v['goodsId'];

            }
        }

        if (!empty($goodsData2)) {

            foreach ($goodsData2 as $k => $v) {

                $ids[] = $v['goodsId'];
            }
        }

        if (!empty($ids)) {

            $goodsData = $goodsModel
                ->orderBy('goods_order', 'DESC')
                ->whereIn('goods_id', $ids)
                ->getDdvPageHumpArray();

            foreach ($goodsData['lists'] as $k2 => $v2) {

                $goodsData['lists'][$k2]['goodsSpecialPrice'] = empty($v2['goodsSpecialPrice']) ? array() : json_decode($v2['goodsSpecialPrice'],true);
                $goodsData['lists'][$k2]['goodsIntroduce'] = empty($v2['goodsIntroduce']) ? array() : json_decode($v2['goodsIntroduce'], true);//其他图片
            }
        }

        return $goodsData;

    }

    /*
     * 获取首页显示的通知
     */
    public static function homeNotice() {

        $noticeModel = new NoticeModel();
        $noticeData = $noticeModel
            ->orderBy('notice_order', 'DESC')
            ->getHumpArray();

        return ['lists' => $noticeData];
    }

    /*
     * 获取首页显示的轮播图
     */
    public static function homeBroadcast($shopId) {
        $homeData=array();
        $homeModel = new HomeModel();
        $homeData1 = $homeModel// 轮播图和今日推荐
            ->where('home_type', 1)
            ->whereRaw('FIND_IN_SET(?, shop_id)', $shopId)
            ->getHumpArray(['home_id']);

        $homeData2 = $homeModel
            ->where('home_type', 1)
            ->where('shop_id', 0)
            ->getHumpArray(['home_id']);

        if (!empty($homeData1)) {

            foreach ($homeData1 as $k => $v) {

                $ids[] = $v['homeId'];

            }
        }

        if (!empty($homeData2)) {

            foreach ($homeData2 as $k => $v) {

                $ids[] = $v['homeId'];
            }
        }

        if (!empty($ids)) {

            $homeData = $homeModel
                ->orderBy('home_order', 'DESC')
                ->whereIn('home_id', $ids)
                ->getHumpArray();
        }



        return ['lists' => $homeData];
    }

    /*
     * 获取首页显示的今日推荐
     */
    public static function homeRecommend($shopId) {
        $homeData=array();
        $homeModel = new HomeModel();
        $homeData1 = $homeModel// 轮播图和今日推荐
            ->where('home_type', 2)
            ->whereRaw('FIND_IN_SET(?, shop_id)', $shopId)
            ->getHumpArray();

        $homeData2 = $homeModel
            ->where('home_type', 2)
            ->where('shop_id', 0)
            ->getHumpArray(['home_id']);

        if (!empty($homeData1)) {

            foreach ($homeData1 as $k => $v) {

                $ids[] = $v['homeId'];

            }
        }

        if (!empty($homeData2)) {

            foreach ($homeData2 as $k => $v) {

                $ids[] = $v['homeId'];
            }
        }

        if (!empty($ids)) {

            $homeData = $homeModel
                ->orderBy('home_order', 'DESC')
                ->whereIn('home_id', $ids)
                ->getHumpArray();
        }

        return ['lists' => $homeData];

    }


}
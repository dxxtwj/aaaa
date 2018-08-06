<?php

namespace App\Logic\Shangrui\Api\Home;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Home\HomeModel;
use App\Model\Shangrui\Type\TypeModel;

class HomeLogic extends ShoppingLogic
{
    //首页推荐
    public static function homeGoods(){
        $goodsModel = new GoodsModel();
        $goodsWhere['goods_is_recommend'] = 1;//首页显示
        $res = $goodsModel->where($goodsWhere)->getDdvPageHumpArray();
        return $res;
    }
    //首页新品
    public static function homeNewGoods(){
        $goodsModel = new GoodsModel();
        $res = $goodsModel->where('goods_is_new',1)->getDdvPageHumpArray();
        return $res;
    }
    //首页轮播图
    public static function homeHome(){
       $homeModel = new HomeModel();
       $res = $homeModel->orderBy('home_order','DESC')->getDdvPageHumpArray();
       return $res;
    }
    //首页类别名称
    public static function showType()
    {
        $typeModel = new TypeModel();
        $res = $typeModel->orderBy('type_order', 'DESC')->getDdvPageHumpArray();
        return $res;
    }
    //首页推荐类别查询
    public static function homeRecommendType(){
        $typeModel = new TypeModel();
        $res = $typeModel->where('type_is_recommend',1)->orderBy('type_order','DESC')->getDdvPageHumpArray();
        return $res;
    }


}
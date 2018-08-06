<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

/*
 * 这个是微商城后台的公共方法
 */

namespace App\Logic\Common;
use App\Logic\Shopping\Common\LoadDataLogic;
use App\Model\Shopping\Cart\CartModel;
use App\Model\Shopping\Goods\GoodsModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

class ShoppingLogic extends LoadDataLogic
{
    /*
     * 判断登录
     */
    public static function isLogin($name='') {
        $id = \Session::get($name);
        if (!empty($id)) {
            return $id;
        } else {
            return false;
        }
    }

    /*
     * 查询门店下的数据
     * @param array $data  数据
     * @param string $shopId 商家ID
     * @param string $v['shopId'] 等于0表示该商品属于所有门店的
     * reuturn array
     */
    public static function getType($data, $shopId) {
        if (!empty($data['lists'])) {

        } elseif (empty($data['lists'])) {
            foreach ($data as $k => $v) {
                $bool = strpos($v['shopId'], $shopId);
                if (is_int($bool) || (int)$v['shopId'] == 0) {
                    $arr[] = $data[$k];
                }
            }
        }
        if (!empty($arr)) {
            return $arr;
        } else {
            return array();
        }
    }

    /*
     * 处理商品的价格，客官~~~   分页，单条，没有分页的数组  都可以扔进来给我哦~~~
     * @param array  $goodsRec 商品数据
     * @param string $shopId 商家ID
     * @return array
     */
    public static function goodsPrice($goodsRec, $shopId) {

        if (!empty($goodsRec['lists'])) {//有分页的
            foreach ($goodsRec['lists']as $k2 => $v2) {
                $goodsRec['lists'][$k2]['goodsSpecialPrice'] = empty($v2['goodsSpecialPrice']) ? array() : json_decode($v2['goodsSpecialPrice'],true);
                $goodsRec['lists'][$k2]['goodsIntroduce'] = empty($v2['goodsIntroduce']) ? array() : json_decode($v2['goodsIntroduce'], true);//其他图片
                if (!empty($v2['goodsSpecialPrice'])) {
                    $goodsSpecialPrice = json_decode($v2['goodsSpecialPrice'], true);
                    foreach ($goodsSpecialPrice as $k3 => $v3) {
                        if ($v3['shopId'] ==  $shopId) {
                            $goodsRec['lists'][$k2]['goodsPrice'] = $v3['price'];
                        }
                    }
                }
            }
        } else {
            if (!empty($goodsRec[0])) {//是多维数组 get()
                foreach ($goodsRec as $k => $v) {
                    $goodsRec[$k]['goodsIntroduce'] = empty($goodsRec[$k]['goodsIntroduce']) ? array() : json_decode($goodsRec[$k]['goodsIntroduce'], true);
                    $goodsRec[$k]['goodsSpecialPrice'] = !empty($goodsRec[$k]['goodsSpecialPrice']) ? json_decode($goodsRec[$k]['goodsSpecialPrice'], true) : array();//特殊价格
                    if (!empty($goodsRec[$k]['goodsSpecialPrice'])) {//有特殊价格，则处理特殊价格
                        foreach ($goodsRec[$k]['goodsSpecialPrice'] as $k2 => $v2) {
                            if ($v2['shopId'] == $shopId) {
                                $goodsRec[$k]['goodsPrice'] = $v2['price'];
                            }
                        }
                    }
                }
            } elseif (!empty($goodsRec)) {//一位数组 find()

                $goodsRec['goodsIntroduce'] = empty($goodsRec['goodsIntroduce']) ? array() : json_decode($goodsRec['goodsIntroduce'], true);
                $goodsRec['goodsSpecialPrice'] = !empty($goodsRec['goodsSpecialPrice']) ? json_decode($goodsRec['goodsSpecialPrice'], true) : array();//特殊价格
                if (!empty($goodsRec['goodsSpecialPrice'])) {//有特殊价格，则处理特殊价格
                    foreach ($goodsRec['goodsSpecialPrice'] as $k => $v) {
                        if ($v['shopId'] == $shopId) {
                            $goodsRec['goodsPrice'] = $v['price'];
                        }
                    }
                }
            }
        }
        return $goodsRec;
    }


    /*
     * 具体到年、月的条件 需要查找那个年份，哪个月份
     * @param string $nian 年
     * @param int $time 时间戳
     * return array 返回 例如：2018.5.1   2018.6.1  这种类型的时间戳
     */
    public static function timestamp($nian, $time='') {

        date_default_timezone_set('PRC');

        $time = empty($time) ? time() : $time;
        for ($i = 1; $i <= 13; $i++) {
            $array[] = mktime(00,00,00, $i,1, $nian);//时分秒，月天年

        }

        for ($i = 0; $i <= 13; $i++) {

            if ($array[$i] < $time && $array[$i+1] > $time) {//月份区间

                return array($array[$i], $array[$i+1]);
            }
        }

    }
    /*
     * 具体到日条件  需要查找那一天
     * @param string $yue 月
     * @parma string $ri 日
     * @param string $nian 年
     * return array 返回  例如：  2018.5.5 00：00：00    2018.5.6 00：00：00 这种类型的时间戳
     */
    public static function day($yue,$ri,$nian) {

        date_default_timezone_set('PRC');
        $on = mktime(00,00,00,$yue, $ri, $nian);
        $er = mktime(24,00,00,$yue, $ri, $nian);

        return array($on, $er);
    }


    /*
     * 获取售后信息
     */
    public static function getOrderAlertSale() {

    }


}

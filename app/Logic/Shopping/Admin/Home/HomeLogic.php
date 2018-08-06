<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Home;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\GoodsImg\GoodsImgModel;
use App\Model\Shopping\Home\HomeModel;
use App\Model\Shopping\Shop\ShopModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;

class HomeLogic extends ShoppingLogic
{
    /*
     * 添加首页的信息
     */
    public static function addHome($data) {

        $homeData['home_img'] = empty($data['homeImg']) ? '' : $data['homeImg'];
        $homeData['goods_id'] = empty($data['goodsId']) ? 0 : $data['goodsId'];
        $homeData['home_name'] = empty($data['homeName']) ? '' : $data['homeName'];
        $homeData['home_order'] = empty($data['homeOrder']) ? 0 : $data['homeOrder'];
        $homeData['home_contents'] = empty($data['homeContents']) ? '' : $data['homeContents'];
        $homeData['home_type'] = empty($data['homeType']) ? '' : $data['homeType'];
        $homeData['shop_id'] = empty($data['shopId']) ? '0' : join(',', $data['shopId']);

        if (empty($data['shopId'])) {
            $homeModel = new HomeModel();
            $homeData['shop_id'] = 0;
            $homeModel->setDataByHumpArray($homeData)->save();

        } else {
            $homeModel = new HomeModel();
            $homeModel->setDataByHumpArray($homeData)->save();
        }
    }


    /*
     * 修改
     */
    public static function editHome($data) {

        $homeModel = new HomeModel();

        $homeData['home_img'] = empty($data['homeImg']) ? '' : $data['homeImg'];
        $homeData['goods_id'] = empty($data['goodsId']) ? 0 : $data['goodsId'];
        $homeData['home_name'] = empty($data['homeName']) ? '' : $data['homeName'];
        $homeData['home_order'] = empty($data['homeOrder']) ? 0 : $data['homeOrder'];
        $homeData['home_contents'] = empty($data['homeContents']) ? '' : $data['homeContents'];
        $homeData['home_type'] = empty($data['homeType']) ? '' : $data['homeType'];
        $homeData['shop_id'] = empty($data['shopId']) ? '0' : join(',', $data['shopId']);
        if (empty($data['shopId'])) {

            $homeModel = new HomeModel();
            $bool = $homeModel
                ->where('home_id', $data['homeId'])
                ->updateByHump($homeData);

        } else {

            $bool = $homeModel
                ->where('home_id', $data['homeId'])
                ->updateByHump($homeData);
        }
        if (!$bool) {
            throw new RJsonError('修改失败', 'BROADCAST_ERROR');
        }
        return;
    }


    /*
     * 首页
     * @param array $data['homeId'] 首页标ID
     * @param array $data['homeType'] 1 为轮播图   2 为商品单个推荐  如果为空则查所有类型
     * @return 传ID的话就查单条，返回一位数组，否则查所有  返回二维数组
     */
    public static function showHome($data=array()) {
        $homeModel = new HomeModel();

        if (!empty($data['homeType'])) {//查某种类型

            $homeModel = $homeModel->where('home_type', $data['homeType']);
        }

        if (!empty($data['shopId'])) {

            $homeModel = $homeModel
                ->whereRaw('FIND_IN_SET(?, shop_id)',$data['shopId'])
                ->orWhere('shop_id', 0);
        }

        if (empty($data['homeId'])) {//查全部
            $res = $homeModel->orderBy('home_order', 'DESC')->getDdvPageHumpArray();

        } else {//单条

            $res = $homeModel->orderBy('home_order', 'DESC')->where('home_id', $data['homeId'])->firstHumpArray();

            $arrayShopId = explode(',', $res['shopId']);

            $shopModel = new ShopModel();
            $shopData = $shopModel->whereIn('shop_id', $arrayShopId)->getHumpArray();

            $res['shopData'] = empty($shopData) ? array() : $shopData;

            return ['data' => $res];//这里终止，下面不执行

        }

        return $res;

    }

    /*
     * 删除
     */
    public static function deleteHome($homeId) {

        $homeModel = new HomeModel();

        $bool = $homeModel->where('home_id', $homeId)->delete();

        if (!$bool) {

            throw new RJsonError('删除失败', 'BROADCAST_ERROR');
        }
        return ;
    }

}
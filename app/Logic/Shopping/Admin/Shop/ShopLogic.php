<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Shop;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Shop\ShopModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;

class ShopLogic extends ShoppingLogic
{
    /*
     * 添加商家
     * @param  array $data 要添加的商家数据
     * @param int $lastId 添加成功返回ID
     * @param whereShop() 判断商家店铺表是否有相同数据
     * @return null
     */
    public static function addShop($data) {

        $shopModel = new ShopModel();

        $bool = $shopModel->where('shop_name', $data['shopName'])->firstHumpArray();
        if (!empty($bool)) {

            throw new RJsonError('该商家店铺名字已存在', 'SHOP_ERROR');
        }

        $bool = $shopModel->where('shop_login_phone', $data['shopLoginPhone'])->firstHumpArray();

        if (!empty($bool)) {
            throw new RJsonError('该手机号已经绑定过门店了', 'SHOP_ERROR');
        }

        $data['lat'] = $data['center']['lat'];
        $data['lng'] = $data['center']['lng'];
        unset($data['center']);

//        $data['shopPassword'] =  md5($data['shopPassword']);//加密密码

        $bool = $shopModel->setDataByHumpArray($data)->save();
//        $lastId = $shopModel->getQueueableId();// 添加成功返回ID

        if (!$bool) {

           throw new RJsonError('添加失败商家', 'SHOP_ERROR');

        }

        return;
    }

    /*
     * 如果shopId存在则查单条，如果不存在则查全部
     * @param array $data 为空则查全部   不为空则查单条
     * @return array 一维或二维数组  返回查询成功的数据
     */
    public static function showShop($data=array()) {

        $shopModel = new ShopModel();
        $type = false;
        if (empty($data)) {//查询全部

            $shopData = $shopModel->orderBy('shop_order', 'DESC')->getDdvPageHumpArray();
            foreach ($shopData['lists'] as $k => $v) {

                $res['lists'][$k] = $v;
                $res['lists'][$k]['center']['lng'] = $v['lng'];
                $res['lists'][$k]['center']['lat'] = $v['lat'];
                unset($res['lists'][$k]['lng']);
                unset($res['lists'][$k]['lat']);
                unset($res['lists'][$k]['shopPassword']);
            }

        } elseif(!empty($data['shopId'])) {//查询单条

            $res = $shopModel->where('shop_id', $data['shopId'])->firstHumpArray();
            $res['center']['lng'] = $res['lng'];
            $res['center']['lat'] = $res['lat'];
            unset($res['lng']);
            unset($res['lat']);
            unset($res['shopPassword']);
            $type = true;
        }
        if (empty($res)) {

            throw new RJsonError('暂无商家店铺数据', 'SHOP_ERROR');
        }

        if (!empty($type)) {

            return ['data' => $res];

        } else {

            return $res;
        }
    }

    /*
     * 修改商家店铺
     * @param  whereShop() 判断字段数据是否存在
     * @return null
     */
    public static function putShop($data) {

        $shopModel = new ShopModel();

//        $bool = self::whereShopping($shopModel,'shop_name', $data['shopName']);
//
//        if ($bool == true) {
//
//            throw new RJsonError('该商家店铺名字已存在', 'SHOP_ERROR');
//        }

        $data['lat'] = $data['center']['lat'];
        $data['lng'] = $data['center']['lng'];
        unset($data['center']);

        $bool = $shopModel->where('shop_id', $data['shopId'])->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('修改商家店铺失败', 'SHOP_ERROR');

        }

        return;
    }

    /*
     * 删除删减店铺
     * @param string $data['shopId'] 商家店铺ID
     * @return null
     */
    public static function deleteShop($data) {

        $shopModel = new ShopModel();
        $bool = $shopModel->where('shop_id', $data['shopId'])->delete();

        if (!$bool) {

            throw new RJsonError('删除商家店铺失败', 'SHOP_ERROR');

        }

        return ;

    }
    /*
     * 商家修改密码
     */
    public static function editPassword($data) {

        $shopModel = new ShopModel();
//        $data['shopPassword'] =  md5($data['shopPassword']);//加密密码
        $bool = $shopModel->where('shop_id', $data['shopId'])->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('修改商家店铺失败', 'SHOP_ERROR');

        }
        $where['shop_login_phone'] = $data['shopLoginPhone'];
        $bool = $shopModel->where($where)->where('shop_id', '<>', $data['shopId'])->firstHumpArray();

        if (!empty($bool)) {
            throw new RJsonError('该手机号已经绑定过门店了', 'SHOP_ERROR');

        }

        return;
    }
}
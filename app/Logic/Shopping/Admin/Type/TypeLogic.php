<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Type;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Shop\ShopModel;
use App\Model\Shopping\ShopType\ShopTypeModel;
use App\Model\Shopping\Type\TypeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;


class TypeLogic extends ShoppingLogic
{
    /*
     * 添加一级分类
     * @param whereShopping() 判断字段数据是否已存在
     * @return null
     */
    public static function addType($data) {

        $typeModel = new TypeModel();//分类表
        $bool1 = $typeModel->where('type_name', $data['typeName'])->firstHumpArray();

        if (!empty($bool1)) {
            throw new RJsonError('该分类名已存在', 'TYPE_ERROR');
        }

        \DB::beginTransaction();//启用事务
        try{

            $typeData['type_name']= !empty($data['typeName']) ? $data['typeName'] : '';
            $typeData['type_img']= !empty($data['typeImg']) ? $data['typeImg'] : '';
            $typeData['type_is_enable']= empty($data['typeIsEnable']) ? 0 : 1;
            $typeData['type_order'] =  empty($data['typeOrder']) ? 0 : $data['typeOrder'];
            $typeData['shop_id'] = empty($data['shopId']) ? 0 : join(',', $data['shopId']);
            $typeData['type_is_navigation'] = empty($data['typeIsNavigation']) ? 0 : 1;

            if (!empty($typeData)) {
                $typeModel
                    ->setDataByHumpArray($typeData)
                    ->save();
                $lastId = $typeModel->getQueueableId();//返回成功的ID

                if (!$lastId) {
                    throw new RJsonError('添加一级分类失败', 'TYPE_ERROR');
                }
            }

            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), 'SHOP_ERROR');
        }
        return;
    }
    /*
     * 添加商家分类关联表
     * @param string $shopId 商家ID
     * @param string $lastId 商品ID
     * @return null
     */
//    public static function addShopType($shopId, $lastId) {
//        $shopTypeModel = new shopTypeModel();
//
//        $shopTypeData['type_id'] = $lastId;// 分类ID
//        $shopTypeData['shop_id'] = $shopId;//商家门店ID
//        $shopTypeBool = $shopTypeModel
//            ->setDataByHumpArray($shopTypeData)
//            ->save();
//
//        if (!$shopTypeBool) {
//
//            throw new RJsonError('添加关联表失败', 'SHOP_ERROR');
//        }
//
//        return;
//    }
    /*
     * 添加子分类
     * @param  array $data 要添加的数据
     * @return null
     */
//    public static function addTypeEr($data) {
//
//        $typeModel = new TypeModel();
//        $bool = self::whereShopping($typeModel, 'type_name', $data['typeName']);
//
//        if ($bool == true) {
//
//            throw new RJsonError('该分类名已存在', 'TYPE_ERROR');
//        }
//        $bool = $typeModel->setDataByHumpArray($data)->save();
//
//        if (!$bool) {
//            throw new RJsonError('添加二级分类失败', 'TYPE_ERROR');
//        }
//
//        return ;
//    }

    /*
     * 修改分类
     * @param array $data 数据
     * @param null
     */
    public static function editType($data) {

        $typeModel = new TypeModel();

        $typeData['type_is_navigation'] = empty($data['typeIsNavigation']) ? 0 : $data['typeIsNavigation'];
        $typeData['type_name']= !empty($data['typeName']) ? $data['typeName'] : '';
        $typeData['type_img']= !empty($data['typeImg']) ? $data['typeImg'] : '';
        $typeData['type_is_enable']= empty($data['typeIsEnable']) ? 0 : 1;
        $typeData['type_order'] =  empty($data['typeOrder']) ? 0 : $data['typeOrder'];
        $typeData['shop_id'] = empty($data['shopId']) ? 0 : join(',', $data['shopId']);


        $bool = $typeModel
            ->where('type_id', $data['typeId'])
            ->updateByHump($typeData);

        if (!$bool) {
            throw new RJsonError('修改分类失败', 'TYPE_ERROR');
        }

        return ;
    }
    /*
     * 如果typeId存在则查单条，如果不存在则查全部,查单条的时候要把商家的数据也查出来
     * @param array $data 为空则查全部   不为空则查单条
     * @param type_is_enable 0 没启用的   1 启用的
     * @return array 一维或二维数组  返回查询成功的数据
     */
    public static function showType($data=array()) {

        $typeModel = new TypeModel();

        $type = false;//判断是否单条的标记

        if (empty($data)) {//查询全部

            $res = $typeModel
                ->orderBy('type_order', 'DESC')
                ->getDdvPageHumpArray();

        } elseif(!empty($data['typeId'])) {//查询单条

            $res = $typeModel
                ->where('type_id', $data['typeId'])
                ->firstHumpArray();

            $type = true;//判断是否单条的标记
        }

        if (empty($res)) {//没有数据

            throw new RJsonError('暂无分类数据', 'TYPE_ERROR');
        }
        if ($type) { // 查单条的时候
            if ($res['shopId'] != 0) {

                $res['shopLists'] = self::shopShow($res['typeId']);
            } else {
                $res['shopLists'] = self::shopShow(0);
            }
            return ['data' => $res];
        }

        return $res;
    }

    /*
     * 通过点击不同的分类寻找商家
     * @param sting $typeId 分类ID  为0 时候则找所有商家
     * @return array 二维数组的商家数据
     */
    public static function shopShow($typeId=0) {

        $shopData = array();
        $typeModel = new TypeModel();
        $shopModel = new ShopModel();

        if ($typeId != 0) {

            $typeData = $typeModel
                ->where('type_id', $typeId)
                ->firstHumpArray();

            $shopIdArray = explode(',', $typeData['shopId']);


            $shopData = $shopModel
                ->whereIn('shop_id', $shopIdArray)
                ->getHumpArray();
            foreach ($shopData as $k => $v) {

                $shopData[$k]['center']['lng'] = $v['lng'];
                $shopData[$k]['center']['lat'] = $v['lat'];
                unset($shopData[$k]['lng']);
                unset($shopData[$k]['lat']);
            }

        } elseif($typeId == 0) {//找所有商家

            $shopData = $shopModel
                ->getHumpArray();
            foreach ($shopData as $k => $v) {

                $shopData[$k]['center']['lng'] = $v['lng'];
                $shopData[$k]['center']['lat'] = $v['lat'];
                unset($shopData[$k]['lng']);
                unset($shopData[$k]['lat']);
            }

        }


        return $shopData;
    }
    /*
     * 删除分类
     * @param array $data 数据，里面只有一个ID
     * @param 删除分类后也要去删除分类和商家关联表的数据
     * @return null
     */
    public static function deleteType($data) {

        $goodsModel = new GoodsModel();
        $bool = $goodsModel->where('type_id', $data['typeId'])->firstHumpArray();
        if (!empty($bool)) {
            throw new RJsonError('该分类下面有商品，不能删除', 'TYPE_ERROE');
        }
        $typeModel = new TypeModel();
        $bool = $typeModel->where('type_pid', $data['typeId'])->firstHumpArray();
        if (!empty($bool)) {
            throw new RJsonError('该分类有子分类，不能删除', 'TYPE_ERROE');
        }
        $bool = $typeModel->where('type_id', $data['typeId'])->delete();
        if (!$bool) {
            throw new RJsonError('删除分类失败', 'TYPE_ERROE');
        }
        return ;

    }

    /*
     * 设置为首页分类导航
     * @param string $typeId 分类ID
     * @param int,bool whereStatistics() 成功返回统计后的结果，失败返回false
     * @param int $num 设置首页导航最大数量
     * @param bool whereShopping()
     * @return null
     */
    public static function isNavigation($typeId) {

        $typeModel = new TypeModel();
        $data['type_is_navigation'] = 1;// 0否 1 设置为首页分类导航
        $bool = $typeModel->where('type_id', $typeId)->firstHumpArray();
        if (!empty($bool)) {
            throw new RJsonError('分类不存在', 'BAViGATION_ERROE');
        }
        $num = 4;  // 最大多少条
        $number = $typeModel->where('type_is_navigation', 1)->count();
        if ((int)$number >= $num || (int)$number === 0) {

            throw new RJsonError('添加分类导航最多不能超过'.$num.'个', 'BAViGATION_ERROE');
        }

        $bool =  $typeModel
            ->where('type_id', $typeId)
            ->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('添加分类导航失败', 'BAViGATION_ERROE');

        }

        return ;
    }

    /*
     * 取消首页分页导航
     */
    public static function cancelNavigation($typeId) {

        $typeModel = new TypeModel();

        $bool = $typeModel->where('type_id', $typeId)->firstHumpArray();
        if (!empty($bool)) {
            throw new RJsonError('分类不存在', 'BAViGATION_ERROE');
        }

        $data['type_is_navigation'] = 0;// 0否 1 设置为首页分类导航

        $bool =  $typeModel
            ->where('type_id', $typeId)
            ->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('取消分类导航失败', 'BAViGATION_ERROE');

        }
        return ;
    }
}
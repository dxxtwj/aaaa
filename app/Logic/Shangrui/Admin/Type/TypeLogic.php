<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\Type;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Type\TypeModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function GuzzleHttp\Promise\is_fulfilled;
use Illuminate\Database\QueryException;


class TypeLogic extends ShoppingLogic
{
    // 添加一级分类
    public static function addType($data = array()){
        $typeModel = new TypeModel(); //分类表
        $type = $typeModel->where('type_name',$data['typeName'])->firstHumpArray();

        if (!empty($type)){
            throw new RJsonError('该分类已存在', 'TYPE_ERROR');
        }

        $typeData['type_name']= !empty($data['typeName']) ? $data['typeName'] : '';
        $typeData['type_img']= !empty($data['typeImg']) ? $data['typeImg'] : '';
        $typeData['type_order'] =  empty($data['typeOrder']) ? 0 : $data['typeOrder'];
        $typeData['type_is_enable']= empty($data['typeIsEnable']) ? 0 : 1;
        $typeData['type_is_navigation'] = empty($data['typeIsNavigation']) ? 0 : 1;
        $typeData['type_is_recommend'] = empty($data['typeIsRecommend']) ? 0 : 1;
        if (!empty($typeData)) {
            $typeModel
                ->setDataByHumpArray($typeData)
                ->save();
            $lastId = $typeModel->getQueueableId();//返回成功的ID

            if (!$lastId) {
                throw new RJsonError('添加一级分类失败', 'TYPE_ERROR');
            }
        }

    }

    // 修改分类
    public static function editType($data = array()){
        $typeModel = new TypeModel(); // 分类表
        $type = $typeModel->where('type_id',$data['typeId'])->firstHumpArray();


        if (!empty($type)){
            $typeData['type_name'] = empty($data['typeName']) ? $type['typeName'] : $data['typeName'];
            $typeData['type_img'] = empty($data['typeImg']) ? $type['typeImg'] : $data['typeImg'];
            $typeData['type_order'] = empty($data['typeOrder']) ? $type['typeOrder'] : $data['typeOrder'];
            $typeData['type_is_enable'] = empty($data['typeIsEnable']) ? 0 : 1;
            $typeData['type_is_navigation'] = empty($data['typeIsNavigation']) ? 0 : 1;
            $typeData['type_is_recommend'] = empty($data['typeIsRecommend']) ? 0 : 1;
            $bool = $typeModel->where('type_id',$data['typeId'])->updateByHump($typeData);

            if (!$bool){
                throw new RJsonError('修改分类失败','TYPE_ERROR');
            }
        } else {
            throw new RJsonError('分类错误','TYPE_ERROR');
        }
        return ;
    }

    // 查询分类
    public static function showType($data=array()){
        $typeModel = new TypeModel();
        if (empty($data['typeId'])){ //查全部
            $res = $typeModel->orderBy('type_order','DESC')->getDdvPageHumpArray();
            return $res;
        } elseif (!empty($data['typeId'])){
            $res = $typeModel->where('type_id',$data['typeId'])->firstHumpArray();
            return ['data' => $res];
        }

    }

    // 删除分类
    public static function deleteType($data = array()){
        $goodsModel = new GoodsModel();
        $typeModel = new TypeModel();
        $bool = $goodsModel->where('type_id',$data)->firstHumpArray();
        if (!empty($bool)){
            throw new RJsonError('该分类下还有商品，不能删除','TYPE_ERROR');
        }
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

    // 设置首页分类导航
    public static function isNavigation($data = array()){
        $typeModel = new TypeModel();
        //判断分类是否存在
        $bool = $typeModel->where('type_id',$data['typeId'])->firstHumpArray();
        if (empty($bool)){
            throw new RJsonError('分类不存在','TYPE_ERROR');
        }

        $num = 4; //最多多少条
        $number = $typeModel->where('type_is_navigation', 1)->count();

        if ((int)$number >= $num || (int)$number === 0) {

            throw new RJsonError('添加分类导航最多不能超过'.$num.'个', 'TYPE_ERROR');
        }
        $data['type_is_navigation'] = 1;
        $bool =  $typeModel
            ->where('type_id', $data['typeId'])
            ->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('添加分类导航失败', 'TYPE_ERROR');

        }

        return ;
    }

    // 取消首页分类导航
    public static function cancelNavigation($data = array()){
        $typeModel = new TypeModel();
        $bool = $typeModel->where('type_id',$data['typeId'])->firstHumpArray();
        if (empty($bool)){
            throw new RJsonError('分类不存在','TYPE_ERROR');
        }
        $data['type_is_navigation'] = 0;
        $bool =  $typeModel
            ->where('type_id', $data['typeId'])
            ->updateByHump($data);

        if (!$bool) {
            throw new RJsonError('添加分类导航失败', 'TYPE_ERROR');

        }

        return ;
    }

}
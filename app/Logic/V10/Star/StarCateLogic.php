<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Star;

use App\Logic\V10\Common\TreeLogic;
use App\Model\V10\Star\StarCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class StarCateLogic
{

    //添加主表
    public static function add($data=[])
    {
        \DB::beginTransaction();
        try{
            $model = new StarCateModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return $model;
    }

    public static function Lists($data=[])
    {
        $model = new StarCateModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        if(isset($data['starCateTitle'])){
            $model = $model->where('star_cate_title', 'like',$data['starCateTitle']);
        }
        $lists = $model->getHumpArray();
        $res = TreeLogic::starTree($lists);
        return $res;
    }

    public static function getOne($starCateId)
    {
        $model = new StarCateModel();
        $data = $model->where('star_cate_id',$starCateId)->firstHumpArray(['*']);
        return $data;
    }
    public static function getCateById($newsCateId)
    {
        $model = new StarCateModel();
        $Admin = $model->where('pid',$newsCateId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new StarCateModel();
            $model->where('star_cate_id', $data['starCateId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($starCateId)
    {
        \DB::beginTransaction();
        try{
            //查看是否有子类
            $res = self::getCateById($starCateId);
            if(!empty($res)){
                throw new RJsonError('该分类下还有子类', 'NEWS_CATE_ERROR');
            }
            $new = StarLogic::getByCateId($starCateId);
            if(!empty($new)){
                throw new RJsonError('该分类下还有数据', 'NEWS_ERROR');
            }
            $model = new StarCateModel();
            $model->where('star_cate_id',$starCateId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //获取类下的所有子类ID
    public static function getCateId($starCateId)
    {
        $model = new StarCateModel();
        $data = $model->where('pid',$starCateId)->getHumpArray(['pid','starCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['starCateId'];
            }
        }
        $arr2 = self::StarTree($arr);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $starCateId=(int) $starCateId;
        array_push($res,$starCateId);
        return $res;
    }
    public static function StarTree($starCateId) {
        $model = new StarCateModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereIn('pid',$starCateId)->getHumpArray(['pid','starCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['starCateId'];
                $arr2 =self::StarTree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }

    public static function getRecommend($data)
    {
        $model = new StarCateModel();
        $res = $model->where('recommend',1)->firstHumpArray(['*']);
        return $res;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Cases;

use App\Logic\V10\Common\TreeLogic;
use App\Model\V10\Cases\CasesCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class CasesCateLogic
{
    //添加主表
    public static function add($data=[])
    {
        \DB::beginTransaction();
        try{
            $model = new CasesCateModel();
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
        $model = new CasesCateModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        if(isset($data['casesCateTitle'])){
            $model = $model->where('cases_cate_title', 'like',$data['casesCateTitle']);
        }
        $lists = $model->getHumpArray();
        $res = TreeLogic::casesTree($lists);
        return $res;
    }

    public static function getOne($casesCateId)
    {
        $model = new CasesCateModel();
        $Admin = $model->where('cases_cate_id',$casesCateId)->firstHumpArray(['*']);
        return $Admin;
    }

    public static function getCateById($casesCateId)
    {
        $model = new CasesCateModel();
        $Admin = $model->where('pid',$casesCateId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new CasesCateModel();
            $model->where('cases_cate_id', $data['casesCateId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($casesCateId)
    {
        \DB::beginTransaction();
        try{
            //查看是否有子类
            $res = self::getCateById($casesCateId);
            if(!empty($res)){
                throw new RJsonError('该分类下还有子类', 'NEWS_CATE_ERROR');
            }
            $new = CasesLogic::getByCateId($casesCateId);
            if(!empty($new)){
                throw new RJsonError('该分类下还有数据', 'NEWS_ERROR');
            }
            $model = new CasesCateModel();
            $model->where('cases_cate_id',$casesCateId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //获取类下的所有子类ID
    public static function getCateId($casesCateId)
    {
        $model = new CasesCateModel();
        $data = $model->where('pid',$casesCateId)->getHumpArray(['pid','casesCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['casesCateId'];
            }
        }
        $arr2 = self::CasesTree($arr);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $casesCateId=(int) $casesCateId;
        array_push($res,$casesCateId);
        return $res;
    }
    public static function CasesTree($casesCateId) {
        $model = new CasesCateModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereIn('pid',$casesCateId)->getHumpArray(['pid','casesCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['casesCateId'];
                $arr2 =self::CasesTree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }

    public static function getRecommend($cateId)
    {
        $model = new CasesCateModel();
        $res = $model->where('recommend',1)->where('cases_cate_id',$cateId)->firstHumpArray(['cases_cate_id','cases_cate_title']);
        return $res;
    }


}
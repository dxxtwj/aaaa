<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\PlanModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class PlanLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['content'])){
            $data['content'] = '';
        }
        \DB::beginTransaction();
        try{
            $model = new PlanModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function Lists($data=[])
    {
        $model = new PlanModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        if(isset($data['planTitle'])){
            $model = $model->where('plan_title', 'like','%' . $data['planTitle'] . '%');
        }
        $lists = $model->orderBy('sort','DESC')->getDdvPageHumpArray();
        return $lists;
    }

    public static function getOne($planId)
    {
        $model = new PlanModel();
        $Admin = $model->where('plan_id',$planId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new PlanModel();
            $model->where('plan_id', $data['planId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //
    public static function delete($planId)
    {
        \DB::beginTransaction();
        try{
            $model = new PlanModel();
            $model->where('plan_id',$planId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //推荐
    public static function recommend($number)
    {
        $model = new PlanModel();
        $res = $model->where('is_on',1)->where('recommend',1)->limit($number)->getHumpArray(['*']);
        return $res;
    }


}
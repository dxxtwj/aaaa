<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\BannerModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class BannerLogic
{

    //添加主表
    public static function addBanner($data=[])
    {
        \DB::beginTransaction();
        try{
            $model = new BannerModel();
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
        $model = new BannerModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        $lists = $model->orderBy('sort','DESC')->getHumpArray();
        return $lists;
    }

    public static function getOne($bannerId)
    {
        $model = new BannerModel();
        $Admin = $model->where('banner_id',$bannerId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new BannerModel();
            $model->where('banner_id', $data['bannerId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //
    public static function delete($bannerId)
    {
        \DB::beginTransaction();
        try{
            $model = new BannerModel();
            $model->where('banner_id',$bannerId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }


}
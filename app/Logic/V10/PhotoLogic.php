<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\PhotoModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class PhotoLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['content'])){
            $data['content'] = '';
        }
        \DB::beginTransaction();
        try{
            $model = new PhotoModel();
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
        $model = new PhotoModel();
        if(isset($data['isOn'])){
            $model = $model->where('is_on',1);
        }
        if(isset($data['photoTitle'])){
            $model = $model->where('photo_title', 'like','%' . $data['photoTitle'] . '%');
        }
        $lists = $model->orderBy('sort','DESC')->getDdvPageHumpArray();
        return $lists;
    }

    public static function getOne($photoId)
    {
        $model = new PhotoModel();
        $Admin = $model->where('photo_id',$photoId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new PhotoModel();
            $model->where('photo_id', $data['photoId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //
    public static function delete($photoId)
    {
        \DB::beginTransaction();
        try{
            $model = new PhotoModel();
            $model->where('photo_id',$photoId)->delete();
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
        $model = new PhotoModel();
        $res = $model->where('is_on',1)->where('recommend',1)->limit($number)->getHumpArray(['*']);
        return $res;
    }

}
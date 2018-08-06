<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\LinkModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class LinkLogic
{

    //添加主表
    public static function add($data=[])
    {
        \DB::beginTransaction();
        try{
            $model = new LinkModel();
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
        $model = new LinkModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        $lists = $model->orderBy('sort','DESC')->getHumpArray();
        return $lists;
    }

    public static function getOne($linkId)
    {
        $model = new LinkModel();
        $Admin = $model->where('link_id',$linkId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new LinkModel();
            $model->where('link_id', $data['linkId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //
    public static function delete($linkId)
    {
        \DB::beginTransaction();
        try{
            $model = new LinkModel();
            $model->where('link_id',$linkId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }


}
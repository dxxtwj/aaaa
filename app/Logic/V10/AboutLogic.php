<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\AboutModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class AboutLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['aboutContent'])){
            $data['aboutContent'] = '';
        }
        if($data['aboutTitle']=='公司简介'){
            $res = self::getByName($data['aboutTitle']);
            if(!empty($res)){
                throw new RJsonError('该名称已存在', 'ABOUT_TITLE_ERROR');
            }
            $data['aboutId']=4;
        }
        if($data['aboutTitle']=='联系我们'){
            $res = self::getByName($data['aboutTitle']);
            if(!empty($res)){
                throw new RJsonError('该名称已存在', 'ABOUT_TITLE_ERROR');
            }
            $data['aboutId']=7;
        }
        \DB::beginTransaction();
        try{
            $model = new AboutModel();
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
        $model = new AboutModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        $lists = $model->orderBy('sort','ASC')->getHumpArray();
        return $lists;
    }

    public static function getOne($aboutId)
    {
        $model = new AboutModel();
        $res = $model->where('about_id',$aboutId)->firstHumpArray(['*']);
        return $res;
    }

    public static function getByName($aboutTitle)
    {
        $model = new AboutModel();
        $res = $model->where('about_title',$aboutTitle)->firstHumpArray(['*']);
        return $res;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new AboutModel();
            $model->where('about_id', $data['aboutId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //
    public static function delete($aboutId)
    {
        if($aboutId==4){
            throw new RJsonError('不能删除', 'ABOUT_ERROR');
        }
        \DB::beginTransaction();
        try{
            $model = new AboutModel();
            $model->where('about_id',$aboutId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }


}
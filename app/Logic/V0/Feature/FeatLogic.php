<?php

namespace App\Logic\V0\Feature;
use App\Model\V0\Feature\FeatureModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class FeatLogic
{
    public static function addFeat ($data=[])
    {
        $model = new FeatureModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //获取列表
    public static function getFeatLists ($data)
    {
        $model = new FeatureModel();
        if(isset($data['siteId'])){
            $model = $model->where('site_id',$data['siteId']);
        }
        $model = $model->orderBy('site_id','ASC');
        $FeatLists= $model->select();
        return $FeatLists->getDdvPageHumpArray(true);
    }

    //获取单条
    public static function getFeatOne($FeatId)
    {
         $Feat = (new FeatureModel)->where('feature_id',$FeatId)->firstHumpArray(['*']);
         return $Feat;
    }

    //删除
    public static function delFeat($FeatId)
    {
        (new FeatureModel())->where('feature_id',$FeatId)->delete();
    }

    //编辑
    public static function editFeat($data,$FeatId)
    {
        (new FeatureModel)->where('feature_id',$FeatId)->updateByHump($data);
    }


}
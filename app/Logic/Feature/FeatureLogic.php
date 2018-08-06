<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Feature;

use App\Model\Feature\FeatureModel;
use App\Model\LanguageModel;
use App\Model\SiteLangModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class FeatureLogic
{
    //获取列表
    public static function getFeatureLists()
    {
        $siteLists = FeatureModel::whereSiteId()
            ->select(['*']);
        return $siteLists->getDdvPageHumpArray(true);
    }

    public static function editFeature($data)
    {
        FeatureModel::where('feature_id', $data['featureId'])->updateByHump($data);
    }

}
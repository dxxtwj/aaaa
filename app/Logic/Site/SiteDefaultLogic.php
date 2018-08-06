<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */
namespace App\Logic\Site;

use App\Logic\BasicLogic;
use App\Model\Site\SiteDefaultModel;
use App\Logic\AboutLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class SiteDefaultLogic
{


    //获取住单条
    public static function getSiteDefault($siteId)
    {
        $Default = SiteDefaultModel::where('site_id', $siteId)->firstHump(['is_add']);
        return $Default->isAdd;
    }

    //添加默认数据
    public static function addSiteDefault($siteId,$languageId)
    {
        //再判断一下，存不存在数据
        $about=AboutLogic::getdefault();
        if(empty($about)){
            //添加关于我们
            AboutLogic::addAboutDefault($siteId,$languageId);
        }
        //添加基本信息
        $basic = BasicLogic::getBasic();
        if(empty($basic)){
            BasicLogic::addBasicDefault($siteId,$languageId);
        }
    }

    //改变状态
    public static function putSiteDefault($siteId)
    {
        $data['isAdd']=1;
        $model = new SiteDefaultModel();
        $model->where('site_id', $siteId)->updateByHump($data);
    }

}
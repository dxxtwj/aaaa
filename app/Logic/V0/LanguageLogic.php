<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0;

use App\Model\V0\LanguageModel;
use App\Model\V0\Site\SiteLangModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LanguageLogic
{
    //添加主表
    public static function addLanguage ($data=[])
    {
        $model = new LanguageModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //获取列表
    public static function getLanguageList()
    {
        $LanguageLists = (new LanguageModel)->getHumpArray(['*']);
        return $LanguageLists;
    }
    //获取列表--测试
    public static function getLanguage()
    {
        $model = new LanguageModel();
        $LanguageLists = $model->limit(2)->getHumpArray(['*']);
        return $LanguageLists;
    }
    public static function getSiteLanguage()
    {
        $model = new SiteLangModel();
        $LanguageLists = $model->whereSiteId()
            ->leftjoin('language', 'site_language.language_id', '=', 'language.language_id')
            ->getHumpArray(['site_language.*','language.language_title']);
        return $LanguageLists;
    }

    //获取住单条
    public static function getLanguageOne($languageId)
    {
        $Language = (new LanguageModel)->where('language_id', $languageId)->firstHumpArray(['*']);
        return $Language;
    }

    //编辑
    public static function editLanguage($data=[],$LanguageId)
    {
        (new LanguageModel)->where('language_id', $LanguageId)->updateByHump($data);

    }

    //删除主
    public static function deleteLanguage($LanguageId)
    {
        (new LanguageModel())->where('language_id', $LanguageId)->delete();
        return;
    }



}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Model\LanguageModel;
use App\Model\SiteLangModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LanguageLogic
{

    //获取列表
    public static function getLanguageList()
    {
        $LanguageLists = LanguageModel::select(['*']);
        return $LanguageLists->getHumpArray();
    }

    public static function getSiteLanguage()
    {
        $LanguageLists = SiteLangModel::whereSiteId()
            ->leftjoin('language', 'site_language.language_id', '=', 'language.language_id')
            ->getHumpArray(['site_language.*','language.language_title','language.language_key']);
        return $LanguageLists;
    }

    //获取住单条
    public static function getLanguageOne($languageId)
    {
        $Language = LanguageModel::where('language_id', $languageId)->firstHump(['*']);
        return $Language;
    }

    //获取住单条
    public static function getLanguageId($key)
    {
        $Language = LanguageModel::where('language_key', $key)->firstHumpArray(['*']);
        return $Language['languageId'];
    }

    //获取语言数组
    public static function getLanguageIdArray()
    {
        $LanguageLists = SiteLangModel::whereSiteId()
            ->leftjoin('language', 'site_language.language_id', '=', 'language.language_id')
            ->getHumpArray(['site_language.*','language.language_title','language.language_key']);
        $res = array_column($LanguageLists,'languageId');
        return $res;
    }
}
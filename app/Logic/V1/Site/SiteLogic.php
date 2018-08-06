<?php

namespace App\Logic\V1\Site;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \App\Logic\V2\Common\LoadDataLogic;
use App\Model\V1\Site\SiteModel;
use App\Model\V1\Domain\DomainModel;
class SiteLogic extends LoadDataLogic
{

    public static function getSiteId($host){
        $site = (new SiteModel())->getHump();
        if(empty($site)){
            throw new RJsonError('站点未启用', 'NOT_FIND_SITEID');
        }
        $siteDomainArray = [];
        foreach($site as $siteKey => $siteItem){
            $siteDomain = (new DomainModel())->where(['site_id' => $siteItem->siteId])->select('domain')->getHumpArray();
            if(!empty($siteDomain) && is_array($siteDomain)){
                foreach($siteDomain as $domainKey => $domainItem){
                    $siteDomainArray[] = $domainItem['domain'];
                }
                if(in_array($host, $siteDomainArray)){
                    return $siteItem->siteId;
                }
            }
        }
        throw new RJsonError('域名不可用', 'NOT_FIND_SITE');
    }

    public static function getLanguageId($lang){
        if(empty($lang)){
            throw new RJsonError('语言不能为空', 'NOT_FIND_LANG');
        }
        switch ($lang){
            case 'cn':
            case 'zh-CN':
                $languageId = 1;
                break;
            case 'en':
                $languageId = 2;
                break;
            default:
                throw new RJsonError('未定义语言', 'NOT_FIND_LANG');
        }
        return $languageId;
    }
}
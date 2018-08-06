<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */
namespace App\Logic\Site;

use App\Model\DomainModel;
use App\Logic\Site\SiteLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class DomainLogic
{


    //获取住单条
    public static function getDomainOne($domainId)
    {
        $Domain = DomainModel::where('domain_id', $domainId)->firstHump(['*']);
        return $Domain;
    }


    //根据域名获取站点ID
    public static function getDomain($domainUrl)
    {
        $Domain = DomainModel::where('domain_url', $domainUrl)->where('is_del',1)->firstHump(['site_id']);
        if(empty($Domain)){
            throw new RJsonError('网站维护中...bbb', 'NO_SITE');
        }
        //查看站点是否被放回回收站，状态
        $site=SiteLogic::checkSite($Domain->siteId);
        if(empty($site)){
            throw new RJsonError('网站维护中..........aaa', 'NO_SITE');
        }
        return $Domain->siteId;
    }


}
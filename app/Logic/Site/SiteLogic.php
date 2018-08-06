<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Site;

use App\Logic\LanguageLogic;
use App\Model\DomainModel;
use App\Model\Site\TemplateModel;
use App\Model\Site\TemplateDescModel;
use App\Model\Site\DomainToTemplateModel;

use App\Model\SiteLangModel;
use App\Model\SiteModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class SiteLogic
{

    //获取单条
    /*public static function getSiteOne($siteId)
    {
       $site = SiteModel::where('site_id', $siteId)->firstHump(['*']);
       if($site){
           $lang = self::getSiteLang($siteId);
           if(isset($lang)){
               foreach ($lang as $key=>$value) {
                   if($value['isOn']==1){
                       $lang[$key]['isOn']=true;
                   }else{
                       $lang[$key]['isOn']=false;
                   }
                   //获取语言名称
                    $language = LanguageLogic::getLanguageOne($value['languageId']);
                   $lang[$key]['languageTitle']=empty($language['languageTitle']) ? '' : $language['languageTitle'];
               }
           }
           $site['lang'] = empty($lang) ? '' : $lang;
       }
       return $site;

    }*/

    //获取站点的语言
    /*public static function getSiteLang($siteId)
    {
        $lang = SiteLangModel::where('site_id', $siteId)->getHumpArray(['*']);
        return $lang;

    }*/
    //获取站点的语言列表
    public static function getLangLists($siteId)
    {
        $lang = SiteLangModel::where('site_id', $siteId)->getHumpArray(['language_id']);
        return $lang;

    }


    //获取站点的logo
    public static function getSiteLogo()
    {
        $logo = SiteModel::whereSiteId()->firstHump(['siteLogo']);
        return $logo;

    }



    //检查站点的状态->是否在回收站、禁用
    public static function checkSite($siteId)
    {
        $site = SiteModel::where('site_id', $siteId)
            ->where('is_del',1)
            ->where('site_status',1)
            ->firstHumpArray(['*']);
        return $site;
    }

    public static function getSiteByDomain($data)
    {
        //域名
        $domain = self::getDomain($data['domainUrl']);
        //查看站点状态
        $site=self::checkSite($domain['siteId']);
        $res['siteStatus']=$site['siteStatus'];
        $toTemplate = self::getDomainTemplates($domain['domainId']);
        $res['templateId']=$toTemplate['templateId'];
        return $res;
    }
    public static function getTemplate($templateId)
    {
        $template = TemplateModel::where('template_id',$templateId)->firstHumpArray(['*']);
        return $template;
    }

    public static function getDomain($url)
    {
        $res = DomainModel::where('domain_url',$url)->firstHumpArray(['*']);
        if(empty($res)){
            throw new RJsonError('没有这个域名', 'DOMAIN_ERROR');
        }
        return $res;
    }
    public static function getDomainTemplates($domainId)
    {
        $res = DomainToTemplateModel::where('domain_id',$domainId)->firstHumpArray(['*']);
        if(empty($res)){
            throw new RJsonError('该域名还没有绑定模板', 'DOMAIN_TEMPLATE_ERROR');
        }
        return $res;
    }





}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Site;




use App\Logic\V0\Admin\AdminLogic;
use App\Logic\V0\LanguageLogic;
use App\Model\V0\Site\DomainModel;
use App\Model\V0\Site\SiteDefaultModel;
use App\Model\V0\Site\SiteLangModel;
use App\Model\V0\Site\SiteModel;
use App\Model\V0\Template\DomainToTemplateModel;
use App\Model\V0\Template\TemplateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class SiteLogic
{

    //全部
    public static function addAffair($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteName' => $data['siteName'],
                'siteDesc' => empty($data['siteDesc']) ? '' :$data['siteDesc'],
                'siteLogo' => empty($data['siteLogo']) ? '' : $data['siteLogo'],
                'siteStatus'=>$data['siteStatus']//是否开启
            ];
            $siteId=self::addSite($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'siteId' => $siteId,
                    'languageId'=>$value['languageId'],
                ];
                self::addLang($desc);
            }
            AdminLogic::addAdminDefault($siteId,$data['siteName']);
            //添加默认状态
            $default['siteId']=$siteId;
            self::addDefault($default);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //添加主表
    public static function addSite ($data=[])
    {
        $model = new SiteModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //添加站点语言
    public static function addLang ($data=[])
    {
        $model = new SiteLangModel();
        $model->setDataByHumpArray($data)->save();
        return $model;

    }

    //添加站点默认状态
    public static function addDefault($data=[])
    {
        $model = new SiteDefaultModel();
        $model->setDataByHumpArray($data)->save();
        return $model;

    }

    //获取列表
    public static function getSiteLists($data)
    {
        $model = new SiteModel();
        if (isset($data['siteName'])) {
            $model = $model->where('site_name', 'like', '%'.$data['siteName'].'%');
        }
        $siteLists = $model->where('is_del',$data['isDel'])
            ->orderby('created_at','DESC')//按添加时间排
            ->select(['*']);
        return $siteLists->getDdvPageHumpArray(true);
    }

    //获取单条
    public static function getSiteOne($siteId)
    {
        $model = new SiteModel();
        $site = $model->where('site_id', $siteId)->firstHump(['*']);
        if($site){
            $lang = self::getSiteLang($siteId);
            if(isset($lang)){
                foreach ($lang as $key=>$value) {
                    if($value['isOn']==1){
                        $lang[$key]['isOn']=true;
                    }else{
                        $lang[$key]['isOn']=false;
                    }
                }
            }
            $site['lang'] = empty($lang) ? [] : $lang;
        }
        return $site;
    }

    //获取站点的语言
    public static function getSiteLang($siteId)
    {
        $model = new SiteLangModel();
        $lang = $model->where('site_id', $siteId)->getHumpArray(['*']);
        if(!empty($lang)){
            foreach ($lang as $key=>$value){
                $language = LanguageLogic::getLanguageOne($value['languageId']);
                $lang[$key]['languageTitle'] =empty($language['languageTitle']) ? '' : $language['languageTitle'];
                $lang[$key]['languageKey'] =empty($language['languageKey']) ? '' : $language['languageKey'];
            }
        }
        return $lang;
    }
    //编辑
    public static function editAffair($data,$siteId)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteName' => $data['siteName'],
                'siteLogo' => empty($data['siteLogo']) ? '' : $data['siteLogo'],
                'siteDesc' => empty($data['siteDesc']) ? '' :$data['siteDesc'],
                'siteStatus'=>$data['siteStatus']//是否开启
            ];
            self::editSite($main,$siteId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'isOn' => $value['isOn'],
                ];
                self::editLang($desc,$siteId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //编辑语言--禁用
    public static function editLang($data=[],$siteId,$languageId)
    {
        (new SiteLangModel())->where('site_id', $siteId)->where('language_id',$languageId)->updateByHump($data);
    }

    //编辑
    public static function editSite($data=[],$siteId)
    {
        (new SiteModel())->where('site_id', $siteId)->updateByHump($data);
    }

    //修改状态
    public static function Status($data)
    {
        (new SiteModel())->where('site_id', $data['siteId'])->updateByHump($data);
    }

    //回收站
    public static function delBack($siteId)
    {
        $data['isDel']=0;
        (new SiteModel())->where('site_id', $siteId)->updateByHump($data);
        return;
    }

    //还原
    public static function reduction($siteId)
    {
        $data['isDel']=1;
        (new SiteModel())->where('site_id', $siteId)->updateByHump($data);
        return;
    }

    //删除
    public static function deleteSite($siteId)
    {
        \DB::beginTransaction();
        try{
            (new SiteModel())->where('site_id', $siteId)->delete();
            //语言
            self::deleteSiteLang($siteId);
            //域名
            DomainLogic::deleteDomainBySiteId($siteId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        };
        return;
    }
    //删除语言
    public static function deleteSiteLang($siteId)
    {
        (new SiteLangModel())->where('site_id', $siteId)->delete();
        return;
    }

    //获取单条
    public static function getSiteName($siteId)
    {
        $site = (new SiteModel())->where('site_id', $siteId)->firstHump(['site_name']);
        return $site->siteName;
    }


    public static function getSiteByDomain($data)
    {
        //域名
        $domain = self::getDomain($data['domainUrl']);
        $toTemplate = self::getDomainTemplates($domain['domainId']);
        $template=self::getTemplate($toTemplate['templateId']);
        return $template;
    }
    public static function getTemplate($templateId)
    {
        $template = TemplateModel::where('template_id',$templateId)->firstHumpArray(['template_id']);
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
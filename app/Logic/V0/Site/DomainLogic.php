<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */
namespace App\Logic\V0\Site;


use App\Logic\V0\Template\TemplateLogic;
use App\Model\V0\Site\DomainModel;
use App\Model\V0\Template\DomainToTemplateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function foo\func;
use Illuminate\Database\QueryException;
use function var_dump;

class DomainLogic
{

    //添加事务
    public static function addAffair($data)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId' => $data['siteId'],
                'domainUrl' => $data['domainUrl'],
            ];
            $domainId = self::addDomain($main);
            if(!empty($data['templateId'])){
                $template=[
                    'domainId' => $domainId,
                    'templateId' => $data['templateId']
                ];
                self::addDomainToTemplate($template);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }
    //添加
    public static function addDomain ($data=[])
    {
        $model = new DomainModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //关联模板
    public static function addDomainToTemplate($data)
    {
        $model = new DomainToTemplateModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //关联模板
    public static function getDomainTemplate($domainId)
    {
        $Domain = (new DomainToTemplateModel())->where('domain_id', $domainId)->firstHumpArray(['*']);
        return $Domain;
    }


    //获取列表
    public static function getDomainLists($data)
    {
        $model = new DomainModel();
        if(isset($data['siteId'])){
            $model = $model->where('domain.site_id',$data['siteId']);
        }
        if (isset($data['domainUrl'])) {
            $model = $model->where('domain.domain_url', 'like', '%'.$data['domainUrl'].'%');
        }
        if (isset($data['isDel'])) {
            $model = $model->where('domain.is_del', $data['isDel']);
        }
        $model = $model->orderby('domain.created_at','DESC');
        $DomainLists = $model->leftjoin('site', 'site.site_id', '=', 'domain.site_id')
            ->select([
                'domain.*',
                'site.site_name',
            ]);
        $Domain=$DomainLists->getDdvPageHumpArray(true);
        if($Domain){
            foreach ($Domain['lists'] as $key=>$item) {
                $temp = self::getDomainTemplate($item['domainId']);
                $template = TemplateLogic::getTemplateOne($temp['templateId']);
                $Domain['lists'][$key]['templateId']=empty($template['templateId']) ? '' : $template['templateId'];
                $Domain['lists'][$key]['templateTitle']=empty($template['templateTitle']) ? '' : $template['templateTitle'];
                $Domain['lists'][$key]['templateThumb']=empty($template['templateThumb']) ? '' : $template['templateThumb'];
            }
        }
        return $Domain;
    }

    //获取住单条
    public static function getDomainOne($domainId)
    {
        $Domain = (new DomainModel())->where('domain_id', $domainId)->firstHumpArray(['*']);
        if($Domain){
            $templateId=self::getDomainTemplate($Domain['domainId']);
            $template = TemplateLogic::getTemplateOne($templateId['templateId']);
            $Domain['templateId']=empty($template['templateId']) ? '' : $template['templateId'];
            $Domain['templateTitle']=empty($template['templateTitle']) ? '' : $template['templateTitle'];
            $Domain['templateThumb']=empty($template['templateThumb']) ? '' : $template['templateThumb'];
        }
        return $Domain;
    }

    //获取站点域名--不在回收站
    public static function getDomainBySiteId($siteId)
    {
        $Domain = (new DomainModel())->where('site_id', $siteId)->where('is_del',1)->getHumpArray(['*']);
        return $Domain;
    }

    //获取站点域名--在回收站
    public static function getDomainRecoveryBySiteId($siteId)
    {
        $Domain = (new DomainModel())->where('site_id', $siteId)->where('is_del',0)->getHumpArray(['*']);
        return $Domain;
    }

    //编辑
    public static function editDomain($data=[],$domainId)
    {
        (new DomainModel())->where('domain_id', $domainId)->updateByHump($data);

    }

    //编辑事务
    public static function editAffair($data)
    {
        \DB::beginTransaction();
        try{
            $domainId=$data['domainId'];
            $main=[
                'domainUrl' => $data['domainUrl'],
            ];
            self::editDomain($main,$domainId);
            if(!empty($data['templateId'])){
                $template=[
                    'domainId' => $domainId,
                    'templateId' => $data['templateId']
                ];
                //先获取看有没有
                $res = self::getDomainToTemplate($domainId);
                if(!empty($res)){
                    self::delDomainToTemplate($domainId);
                }
                self::addDomainToTemplate($template);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //获取关联模板
    public static function getDomainToTemplate($domainId)
    {
        $model = new DomainToTemplateModel();
        $res = $model->where('domain_id', $domainId)->firstHumpArray(['*']);
        return $res;
    }

    //删除关联模板
    public static function delDomainToTemplate($domainId)
    {
        $model = new DomainToTemplateModel();
        $model->where('domain_id', $domainId)->delete();
        return;
    }

    //回收站
    public static function delBack($domainId)
    {
        $data['isDel']=0;
        (new DomainModel())->where('domain_id', $domainId)->updateByHump($data);
        return;
    }

    //还原
    public static function reduction($domainId)
    {
        $data['isDel']=1;
        (new DomainModel())->where('domain_id', $domainId)->updateByHump($data);
        return;
    }

    //删除主
    public static function deleteDomain($domainId)
    {
        \DB::beginTransaction();
        try{
            (new DomainModel())->where('domain_id', $domainId)->delete();
            self::delDomainToTemplate($domainId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //根据站点ID删除
    public static function deleteDomainBySiteId($siteId)
    {
        (new DomainModel())->where('site_id', $siteId)->delete();
        return;
    }

    //根据域名获取站点ID
    public static function getDomain($domainUrl)
    {
        $Domain = (new DomainModel())->where('domain_url', $domainUrl)->firstHump(['site_id']);
        return $Domain->siteId;
    }


}
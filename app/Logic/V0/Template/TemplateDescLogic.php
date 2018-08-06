<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Template;

use App\Model\V0\Template\TemplateDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class TemplateDescLogic
{
    //添加
    public static function add($data)
    {
        foreach ($data as $key=>$value){
            //先判断这个链接存不存在
           /*$url = file_exists(__DIR__ . $value['url']);
           if($url){
               self::addTemplateDesc($value);
           }else{
                throw new RJsonError('文件不存在', 'NOT_FIND_FILE');
           }*/
            self::addTemplateDesc($value);
        }
    }

    //添加
    public static function addTemplateDesc ($data=[])
    {
        $model = new TemplateDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    public static function getDescListByTemplateId($data)
    {
        $model = new TemplateDescModel();
        if (isset($data['name'])) {
            $model = $model->where('template_description.name','like','%' . $data['name'] . '%');
        }
        if (isset($data['url'])) {
            $model = $model->where('template_description.url','like','%' . $data['url'] . '%');
        }
        $TemplateDesc = $model->where('template.template_id',$data['templateId'])
            ->leftjoin('template', 'template_description.template_id', '=', 'template.template_id')
            ->select(['template_description.*','template.template_title']);
        return $TemplateDesc->getDdvPageHumpArray(true);
    }

    //查单条
    public static function getTemplateDescOne($templateDescId)
    {
        $Template = (new TemplateDescModel())->where('template_desc_id', $templateDescId)->firstHumpArray(['*']);
        if(!empty($Template)){
            $temp=TemplateLogic::getTemplateOne($Template['templateId']);
            $Template['templateTitle'] = empty($temp['templateTitle']) ? '' : $temp['templateTitle'];
        }
        return $Template;
    }

    public static function getTemplateDescUrl($url)
    {
        $Template = (new TemplateDescModel())->where('url', $url)->firstHumpArray(['*']);
        if(!empty($Template)){
            $temp=TemplateLogic::getTemplateOne($Template['templateId']);
            $Template['templateTitle'] = empty($temp['templateTitle']) ? '' : $temp['templateTitle'];
        }
        return $Template;
    }

    //编辑主表
    public static function editTemplateDesc($data)
    {
        (new TemplateDescModel())->where('template_desc_id', $data['templateDescId'])->updateByHump($data);
    }

    //删除主
    public static function deleteTemplateDesc($templateDescId)
    {
        (new TemplateDescModel())->where('template_desc_id', $templateDescId)->delete();
    }





}
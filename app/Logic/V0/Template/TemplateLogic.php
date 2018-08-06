<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Template;

use App\Model\V0\Template\TemplateDescModel;
use App\Model\V0\Template\TemplateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class TemplateLogic
{
    //添加主表
    public static function addTemplate ($data=[])
    {
        $model = new TemplateModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getTemplateList($data)
    {
        $model = new TemplateModel();
        if(isset($data['templateCateId'])){
            $model = $model->where('template.template_cate_id',$data['templateCateId']);
        }
        if (isset($data['templateTitle'])) {
            $model = $model->where('template.template_title', 'like', '%' . $data['templateTitle'] . '%');
        }
        if(isset($data['isOn'])){
            $model = $model->where('template.is_on',1);
        }
        $model = $model->orderBy('template.sort','DESC');
        $templateLists = $model->leftjoin('template_category', 'template.template_cate_id', '=', 'template_category.template_cate_id')
            ->select([
                'template.*',
                'template_category.template_cate_title'
            ]);
        $lists = $templateLists->getDdvPageHumpArray(true);
        //前端
        if(!empty($lists) && !empty($data['type']) && $data['type']=='api'){
            foreach ($lists['lists'] as $key=>$value){
                $desc = self::getTemplateDesc($value['templateId']);
                $lists['lists'][$key]['desc'] = empty($desc) ? [] : $desc;
            }
        }
        return $lists;
    }

    public static function getTemplateDesc($templateId)
    {
        $templateDesc = (new TemplateDescModel())->where('template_id',$templateId)->getHumpArray(['*']);
        return $templateDesc;
    }

    //查单条
    public static function getTemplateOne($templateId)
    {
        $Template = (new TemplateModel())->where('template_id', $templateId)->firstHumpArray(['*']);
        return $Template;
    }

    //编辑主表
    public static function editTemplate($data)
    {
        (new TemplateModel())->where('template_id', $data['templateId'])->updateByHump($data);

    }

    //删除主
    public static function deleteTemplate($templateId)
    {
        (new TemplateModel())->where('template_id', $templateId)->delete();
    }

    /*
     * 是否显示*/
    public static function isShow($data)
    {
        (new TemplateModel())->where('template_id', $data['templateId'])->updateByHump($data);
    }

    //查看这个类下是否有数据
    public static function getTemplateCate($templateCateId)
    {
        $Template = (new TemplateModel())->where('template_cate_id', $templateCateId)->firstHumpArray(['*']);
        return $Template;
    }

    public static function getRecommend($number)
    {
        $model = new TemplateModel();
        //直接根据recommend等于1查询
        $tempRe=$model->where('recommend',1)->where('is_on',1)->limit($number)->getHumpArray(['*']);
        return $tempRe;
    }


}
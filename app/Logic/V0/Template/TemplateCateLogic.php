<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Template;

use App\Logic\V0\Common\TreeLogic;
use App\Model\V0\Template\TemplateCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class TemplateCateLogic
{
    //添加主表
    public static function addTemplateCate ($data=[])
    {
        $model = new TemplateCateModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getTemplateCateList($data)
    {
        $model = new TemplateCateModel;
        //是否显示
        if(isset($data['isOn'])){
            $model = $model->where('is_on',$data['isOn']);
        }
        if (isset($data['templateCateTitle'])) {
            $model = $model->where('template_cate_title', 'like','%' . $data['templateCateTitle'] . '%');
        }
        $Lists = $model->getHumpArray(['*']);
        $TemplateCate = TreeLogic::Templatetree($Lists);
        return $TemplateCate;

    }

    //查单条
    public static function getTemplateCateOne($templateCateId)
    {
        $Template = (new TemplateCateModel())->where('template_cate_id', $templateCateId)->firstHumpArray(['*']);
        return $Template;
    }

    //查单条
    public static function getParentsId($templateCateId)
    {
        $Template = (new TemplateCateModel())->where('template_cate_id', $templateCateId)->firstHumpArray(['pid']);
        return $Template;
    }

    //编辑主表
    public static function editTemplateCate($data)
    {
        (new TemplateCateModel())->where('template_cate_id', $data['templateCateId'])->updateByHump($data);
    }

    //删除主
    public static function deleteTemplateCate($templateCateId)
    {
        //先看看是否存在子类
        $res = self::getkids($templateCateId);
        if(!empty($res)){
            throw new RJsonError('还有子类', 'CATE_DELETE');
        }
        $res2 = TemplateLogic::getTemplateCate($templateCateId);
        if(!empty($res2)){
            throw new RJsonError('该类下还有数据', 'TEMPLATE_DELETE');
        }
        (new TemplateCateModel)->where('template_cate_id', $templateCateId)->delete();
    }

    //查看是否有下一级
    public static function getkids($templateCateId){
        $Template = (new TemplateCateModel())->where('pid', $templateCateId)->firstHumpArray(['*']);
        return $Template;
    }





}
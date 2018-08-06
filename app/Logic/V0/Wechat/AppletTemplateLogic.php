<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Wechat;

use App\Model\V0\Wechat\AppletTemplateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class AppletTemplateLogic
{
    /*
     * 添加
     * */
    public static function add($data)
    {
        $model = new AppletTemplateModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    /*
     * 获取列表
     * */
    public static function getList()
    {
        $model = new AppletTemplateModel();
        $lists = $model->getDdvPageHumpArray(true);
        return $lists;
    }
    /*
     * 查单条
     * */
    public static function getOne($appletsId)
    {
        $model = new AppletTemplateModel();
        $res = $model->where('applet_template_id', $appletsId)->firstHumpArray(['*']);
        return $res;
    }
    /*
     * 修改
     * */
    public static function edit($data)
    {
        $model = new AppletTemplateModel();
        $model->where('applet_template_id', $data['appletTemplateId'])->updateByHump($data);
        return;
    }
    /*
     * 删除
     * */
    public static function delete($appletTemplateId)
    {
        $model = new AppletTemplateModel();
        $model->where('applet_template_id',$appletTemplateId)->delete();
    }



}
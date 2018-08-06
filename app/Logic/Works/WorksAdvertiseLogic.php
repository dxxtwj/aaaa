<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Works;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Works\WorksAdvertiseModel;
use App\Model\Works\WriterModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class WorksAdvertiseLogic
{
    //添加主表
    public static function addWorksAdvertise ($data=[])
    {
        $model = new WorksAdvertiseModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return;
    }

    //获取列表
    public static function getWorksAdvertiseList($data=null)
    {
        $name=[];
        $show=[];
        if(isset($data['isOn'])){
            $name='is_on';
            $show=$data['isOn'];
        }
        $writer = WorksAdvertiseModel::whereSiteId()->where($name,$show)
            ->select(['*']);
        return $writer->getDdvPageHumpArray(true);
    }

    //查单条
    public static function getWorksAdvertise($writerId)
    {
        $banner = WorksAdvertiseModel::where('advertise_id', $writerId)
            ->firstHump(['*']);
        return $banner;
    }
    public static function getWorksAdvertiseShow($writerId)
    {
        $banner = WorksAdvertiseModel::where('advertise_id', $writerId)->where('is_on',1)
            ->firstHump(['*']);
        return $banner;
    }


    //编辑主表
    public static function editWorksAdvertise($data)
    {
        WorksAdvertiseModel::where('advertise_id', $data['advertiseId'])->updateByHump($data);
    }

    //是否显现
    public static function isShow($data=[],$writerId)
    {
        WorksAdvertiseModel::where('writer_id', $writerId)->updateByHump($data);
    }

    //删除主
    public static function deleteWorksAdvertise($writerId)
    {
        (new WorksAdvertiseModel())->where('writer_id', $writerId)->delete();
    }




}
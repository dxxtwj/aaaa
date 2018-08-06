<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Model\About\HonorModel;
use App\Model\About\AboutDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class HonorLogic
{


    //添加主表
    public static function addHonor ($data=[])
    {
        $model = new HonorModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }



    //获取列表
    public static function getHonorList($data=[])
    {
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='honor.is_on';
            $show=$data['isOn'];
        }
        $AboutLists = HonorModel::whereSiteId()
            ->where($showName,$show)
            ->orderby('honor.sort','DESC')
            ->getHumpArray([
                'honor.*',
            ]);
        return $AboutLists;
    }

    //查主表单条
    public static function getHonorOne($honorId)
    {
        $About = HonorModel::where('honor_id', $honorId)
            ->firstHump(['*']);
        return $About;
    }

    //编辑主表
    public static function editHonor($data=[])
    {
        $honorId=$data['honorId'];
        HonorModel::where('honor_id', $honorId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$honorId)
    {
        HonorModel::where('honor_id', $honorId)->updateByHump($data);
    }

    //删除主
    public static function deleteHonor($honorId)
    {
        (new HonorModel())->where('honor_id', $honorId)->delete();
    }










}
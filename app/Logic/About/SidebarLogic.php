<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\About;

use App\Logic\AboutLogic;
use App\Model\About\SidebarModel;
use App\Model\About\AboutDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class SidebarLogic
{


    //添加主表
    public static function addSidebar ($data=[])
    {
        $model = new SidebarModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }



    //获取列表
    public static function getSidebarList($data=[])
    {
        //是否显示
        $showName=[];
        $show=[];
        $name=[];
        $where=[];
        if(isset($data['isOn'])){
            $showName='sidebar.is_on';
            $show=$data['isOn'];
        }
        //分组
        $groupName=[];
        $groupNum=[];
        if(isset($data['group'])){
            $groupName='sidebar.group';
            $groupNum=$data['group'];
        }
        if(isset($data['languageId'])){
            $name='about_description.language_id';
            $where=$data['languageId'];
        }
        $AboutLists = SidebarModel::whereSiteId()
            ->where($showName,$show)
            ->where($name,$where)
            ->where($groupName,$groupNum)
            ->orderby('sidebar.group','ASC')
            ->orderby('sidebar.sort','DESC')
            ->leftjoin('about_description', 'sidebar.about_id', '=', 'about_description.about_id')
            ->getHumpArray([
                'sidebar.*',
                'about_description.about_title',
            ]);
        if(!empty($AboutLists)){
            foreach ($AboutLists as $key=>$value){
                //获取关于我们
                $res = AboutLogic::getAboutTypeName($value['aboutId']);
                $AboutLists[$key]['aboutType']=$res['aboutType'];
            }
        }
        return $AboutLists;
    }

    //查主表单条
    public static function getSidebarOne($sidebarId,$data=[])
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='about_description.language_id';
            $where=$data['languageId'];
        }
        $About = SidebarModel::whereSiteId()
            ->where('sidebar_id', $sidebarId)
            ->where($name,$where)
            ->leftjoin('about_description', 'sidebar.about_id', '=', 'about_description.about_id')
            ->firstHump([
                'sidebar.*',
                'about_description.about_title',
            ]);
        return $About;
    }

    //编辑主表
    public static function editSidebar($data=[])
    {
        $sidebarId=$data['sidebarId'];
        SidebarModel::where('sidebar_id', $sidebarId)->updateByHump($data);
    }

    //编辑主表
    public static function isShow($data=[],$sidebarId)
    {
        SidebarModel::where('sidebar_id', $sidebarId)->updateByHump($data);
    }

    //删除主
    public static function deleteSidebar($sidebarId)
    {
        (new SidebarModel())->where('sidebar_id', $sidebarId)->delete();
    }

    //根据aboutId获取数据
    public static function getSidebarByAboutId($aboutId)
    {
        $res = SidebarModel::where('about_id', $aboutId)->firstHumpArray(['*']);
        return $res;
    }









}
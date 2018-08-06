<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Menu;

use App\Model\Menu\NavUrlModel;
use App\Model\Menu\NavUrlDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class NavUrlLogic
{
    /*//全部
    public static function addAll ($data=[])
    {
        $mian=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn']
        ];
        $navUrlId=self::addNavUrl($mian);
        foreach ($data['lang'] as $key=>$value){
            $desc=[
                'navUrlId' => $navUrlId,
                'navUrlTitle' => empty($value['navUrlTitle']) ? '' : $value['navUrlTitle'],
                'navUrl' => empty($data['navUrl']) ? '' : $data['navUrl'],
                'languageId'=>$value['languageId'],
            ];
            self::addNavUrlDesc($desc);
        }
    }

    //添加主表
    public static function addNavUrl ($data=[])
    {
        $model = new NavUrlModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addNavUrlDesc ($data=[])
    {
        $model = new NavUrlDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getNavUrlList($data)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='nav_url_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='nav_url.is_on';
            $show=$data['isOn'];
        }
        $linkLists = NavUrlModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->orderby('nav_url.sort','ASC')
            ->leftjoin('nav_url_description', 'nav_url.nav_url_id', '=', 'nav_url_description.nav_url_id')
            ->getHumpArray([
                'nav_url.*',
                'nav_url_description.*',
            ]);
        return $linkLists;
    }

    //获取住单条
    public static function getNavUrlOne($navUrlId)
    {
        $nav = NavUrlModel::where('nav_url_id', $navUrlId)->firstHump(['*']);
        if(isset($nav)){
            $LinkDesc=self::getNavUrlDesc($nav['navUrlId']);
            if(isset($LinkDesc)){
                foreach ($LinkDesc as $key=>$value){
                    $nav['navUrl']=empty($value['navUrl']) ? '' : $value['navUrl'];
                }
            }
        }
        $nav['lang']=empty($LinkDesc) ? '' : $LinkDesc;
        return $nav;
    }
    //获取详情全部
    public static function getNavUrlDesc($navUrlId)
    {
        $LinkDeac = NavUrlDescModel::where('nav_url_id', $navUrlId)
            ->getHump(['*']);
        return $LinkDeac;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $navUrlId=$data['linkId'];
        $mian=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn'],
        ];
        self::editLink($mian,$navUrlId);
        foreach ($data['lang'] as $key=>$value){
            $desc=[
                'navUrlTitle' => empty($value['navUrlTitle']) ? '' : $value['navUrlTitle'],
                'navUrl' => empty($data['navUrl']) ? '' : $data['navUrl'],
            ];
            self::editNavUrlDesc($desc,$navUrlId,$value['languageId']);
        }
    }

    //编辑主表
    public static function editNavUrl($data=[],$navUrlId)
    {
        NavUrlModel::where('nav_url_id', $navUrlId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$navUrlId)
    {
        NavUrlModel::where('nav_url_id', $navUrlId)->updateByHump($data);
    }

    //编辑详细表
    public static function editNavUrlDesc($data=[],$navUrlId,$languageId)
    {
        NavUrlDescModel::where('nav_url_id', $navUrlId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除主
    public static function deleteNavUrl($navUrlId)
    {
        (new NavUrlModel())->where('nav_url_id', $navUrlId)->delete();
        self::deleteNavUrlDesc($navUrlId);
    }
    //删除详
    public static function deleteNavUrlDesc($navUrlId)
    {
        (new NavUrlDescModel())->where('nav_url_id', $navUrlId)->delete();
    }

    //=========================前端调用单条==============================
    //查单条
    public static function getNavUrl($navUrlId,$languageId)
    {
        $banner = NavUrlModel::where('nav_url.nav_url_id', $navUrlId)
            ->where('nav_url_description.language_id',$languageId)
            ->leftjoin('nav_url_description', 'nav_url.nav_url_id', '=', 'nav_url_description.nav_url_id')
            ->getHumpArray([
                'nav_url.*',
                'nav_url_description.*',
            ]);

        return $banner;
    }*/


}
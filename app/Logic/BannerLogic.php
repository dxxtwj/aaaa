<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Banner\BannerModel;
use App\Model\Banner\BannerDescModel;
use App\Logic\Menu\MenuLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class BannerLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn'],//是否显示
            'isOpen'=>$data['isOpen'],//是否打开新窗口
            'menuId'=>$data['menuId'],//菜单ID
            'isUrl'=>empty($data['isUrl']) ? 0 : $data['isUrl'],//是否连接跳转
            'bannerUrlType'=>empty($data['bannerUrlType']) ? 0 : $data['bannerUrlType'],//打开窗口类型
            'menuUrl'=>empty($data['menuUrl']) ? '' : $data['menuUrl'],//链接地址
            'systemType'=>empty($data['systemType']) ? '' : $data['systemType'],//系统类型
            'labelId'=>empty($data['labelId']) ? 0 : $data['labelId'],//套数
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['bannerImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['bannerImage']);
            }
            $bannerId = self::addBanner($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'bannerId' => $bannerId,
                    'galleryId'=>$GalleryId,
                    'bannerTitle' => empty($value['bannerTitle']) ? '' : $value['bannerTitle'],
                    'bannerDesc' => empty($value['bannerDesc']) ? '' : $value['bannerDesc'],
                    'bannerUrl' => empty($data['bannerUrl']) ? '' : $data['bannerUrl'],
                    'bannerImage' => empty($data['bannerImage']) ? '' : $data['bannerImage'],
                    'languageId'=>$value['languageId'],
                ];
                self::addBannerDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addBanner ($data=[])
    {
        $model = new BannerModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addBannerDesc ($data=[])
    {
        $model = new BannerDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getBannerList($data)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='banner_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='banner.is_on';
            $show=$data['isOn'];
        }
        $BannerLists = BannerModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->orderby('banner.sort','DESC')
            ->leftjoin('banner_description', 'banner.banner_id', '=', 'banner_description.banner_id')
            ->getHumpArray([
                'banner.*',
                'banner_description.*',
            ]);
        return $BannerLists;
    }

    //获取菜单ID列表
    public static function getBannerListsByMenuId($data){
        $menuIdLists = BannerModel::whereSiteId()
            ->groupby('menu_id')
            ->getHumpArray([
                'banner.menu_id'
            ]);
        if(!empty($menuIdLists)){
            foreach ($menuIdLists as $key=>$value){
                //获取去菜单导航名称
                $menuName=MenuLogic::getMenuName($value['menuId'],$data['languageId']);
                $menuIdLists[$key]['menuTitle'] = empty($menuName['menuTitle']) ? '' : $menuName['menuTitle'];
                $banner = self::getBannerlists($data,$value['menuId']);
                $menuIdLists[$key]['banners'] = empty($banner) ? [] : $banner;
            }
        }
        return $menuIdLists;
    }

    public static function getBannerlists($data,$menuId)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='banner_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='banner.is_on';
            $show=$data['isOn'];
        }
        $BannerLists = BannerModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->where('menu_id',$menuId)
            ->orderby('banner.sort','DESC')
            ->leftjoin('banner_description', 'banner.banner_id', '=', 'banner_description.banner_id')
            ->getHumpArray([
                'banner.*',
                'banner_description.*',
            ]);
        return $BannerLists;
    }





    //获取住单条
    public static function getBannerOne($bannerId)
    {
        $banner = BannerModel::where('banner_id', $bannerId)->firstHump(['*']);
        if(isset($banner)){
            $BnanerDesc=self::getBannerDesc($banner['bannerId']);
            if(isset($BnanerDesc)){
                foreach ($BnanerDesc as $key=>$value){
                    $banner['bannerImage']=empty($value['bannerImage']) ? '' : $value['bannerImage'];
                    $banner['bannerUrl']=empty($value['bannerUrl']) ? '' : $value['bannerUrl'];
                }
                $banner['lang']=empty($BnanerDesc) ? [] : $BnanerDesc;
            }
        }
        return $banner;
    }
    //获取详情全部
    public static function getBannerDesc($bannerId)
    {
        $NewsDeac = BannerDescModel::where('banner_id', $bannerId)
            ->getHump(['*']);
        return $NewsDeac;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn'],
            'isOpen'=>$data['isOpen'],//是否打开新窗口
            'menuId'=>$data['menuId'],//菜单ID
            'isUrl'=>empty($data['isUrl']) ? 0 : $data['isUrl'],//是否连接跳转
            'bannerUrlType'=>empty($data['bannerUrlType']) ? 0 : $data['bannerUrlType'],//打开窗口类型
            'menuUrl'=>empty($data['menuUrl']) ? '' : $data['menuUrl'],//链接地址
            'systemType'=>empty($data['systemType']) ? '' : $data['systemType'],//系统类型
            'labelId'=>empty($data['labelId']) ? 0 : $data['labelId'],//套数
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['bannerImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['bannerImage']);
            }
            $bannerId=$data['bannerId'];
            self::editBanner($main,$bannerId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'bannerTitle' => empty($value['bannerTitle']) ? '' : $value['bannerTitle'],
                    'bannerDesc' => empty($value['bannerDesc']) ? '' : $value['bannerDesc'],
                    'bannerUrl' => empty($data['bannerUrl']) ? '' : $data['bannerUrl'],
                    'bannerImage' => empty($data['bannerImage']) ? '' : $data['bannerImage'],
                ];
                //有多语言修改
                self::editBannerDesc($desc,$bannerId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editBanner($data=[],$bannerId)
    {
        BannerModel::where('banner_id', $bannerId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$bannerId)
    {
        BannerModel::where('banner_id', $bannerId)->updateByHump($data);
    }

    //编辑详细表----有多语言的修改
    public static function editBannerDesc($data=[],$bannerId,$languageId)
    {
        BannerDescModel::where('banner_id', $bannerId)->where('language_id',$languageId)->updateByHump($data);
    }


    //根据菜单ID删除
    public static function deleteBannerByMenuId($menuId)
    {
        self::getBannerId($menuId);
        (new BannerModel())->where('menu_id', $menuId)->delete();
    }
    public static function getBannerId($menuId){
        $banner = BannerModel::where('menu_id', $menuId)->getHumpArray(['banner_id']);
        if(!empty($banner)){
            foreach ($banner as $value){
                self::deleteBannerDesc($value['bannerId']);
            }
        }
    }

    //删除事务
    public static function delAffair($bannerId)
    {
        \DB::beginTransaction();
        try{
            self::deleteBanner($bannerId);
            self::deleteBannerDesc($bannerId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteBanner($bannerId)
    {
        (new BannerModel())->where('banner_id', $bannerId)->delete();
    }
    //删除详
    public static function deleteBannerDesc($bannerId)
    {
        (new BannerDescModel())->where('banner_id', $bannerId)->delete();
    }

    //=========================前端调用单条==============================

    //查单条
    public static function getBanner($bannerId,$languageId)
    {
        $banner = BannerModel::where('banner.banner_id', $bannerId)
            ->where('banner_description.language_id',$languageId)
            ->leftjoin('banner_description', 'banner.banner_id', '=', 'banner_description.banner_id')
            ->getHumpArray([
                'banner.*',
                'banner_description.*',
            ]);

        return $banner;
    }

    //查
    public static function getBannerByMenuId($menuId,$languageId)
    {
        $banner = BannerModel::where('banner.menu_id', $menuId)
            ->where('banner_description.language_id',$languageId)
            ->leftjoin('banner_description', 'banner.banner_id', '=', 'banner_description.banner_id')
            ->getHumpArray([
                'banner.*',
                'banner_description.*',
            ]);

        return $banner;
    }

    //根据标识拿
    public static function getBannerByUrl($data,$languageId)
    {
        $name=[];
        $labelId=[];
        if(!empty($data['labelId'])){
            $name='banner.label_id';
            $labelId=$data['labelId'];
        }
        $banner = BannerModel::whereSiteId()
            ->where($name,$labelId)
            ->where('banner.system_type', $data['systemType'])
            ->firstHumpArray(['banner.menu_id']);
        $res = BannerModel::where('banner.menu_id', $banner['menuId'])
            ->where('banner_description.language_id',$languageId)
            ->leftjoin('banner_description', 'banner.banner_id', '=', 'banner_description.banner_id')
            ->getHumpArray([
                'banner.*',
                'banner_description.*',
            ]);

        return $res;
    }

    public static function getBannerByMenu($menuId,$languageId)
    {
        $res = MenuLogic::getMenuBanner($menuId,$languageId);
        if(empty($res)){
            throw new RJsonError('没有数据', 'BANNER_ERROR');
        }else{
           $banner = self::getMenuArr($res,$languageId);
            if($banner){
                return $banner;
            }
        }
        return;
    }

    public static function getMenuArr($data,$languageId)
    {
        $arr=array_reverse($data);
        foreach ($arr as $key=>$value){
            $banner = BannerLogic::getBannerByMenuId($value['menuId'],$languageId);
            if($banner){
                return $banner;
            }
        }
        return;
    }

    //-----------------------图库-------------------------

    //清空详情图片
    public static function ClearBannerImage($galleryId)
    {
        $data['bannerImage']='';
        $data['galleryId']=0;
        BannerDescModel::where('gallery_id', $galleryId)->updateByHump($data);
        return;
    }

}
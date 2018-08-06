<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Logic\Gallery\GalleryLogic;
use App\Model\About\AboutModel;
use App\Model\About\AboutDescModel;
use App\Logic\Site\SiteLogic;
use App\Http\Middleware\SiteId;
use App\Model\About\AboutBannerModel;
use App\Model\Banner\BannerModel;
use App\Model\Gallery\GalleryModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class AboutLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'isOn' => $data['isOn'],
            'sort' => $data['sort'],
            'aboutType'=>empty($data['aboutType']) ? '' : $data['aboutType']
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['aboutThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['aboutThumb']);
            }
            $aboutId = self::addAbout($main);
            foreach ($data['lang'] as $value){
                $desc=[
                    'aboutId' => $aboutId,
                    'galleryId'=>$GalleryId,
                    'aboutTitle' => $value['aboutTitle'],
                    'aboutContent' => empty($value['aboutContent']) ? '' : $value['aboutContent'],
                    'aboutThumb' => empty($data['aboutThumb']) ? '' : $data['aboutThumb'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addAboutDesc($desc);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //广告图
            if(isset($data['aboutbanner'])){
                self::addAboutBanner($data['aboutbanner'],$aboutId);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addAbout ($data=[])
    {
        $model = new AboutModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //添加详细表
    public static function addAboutDesc ($data=[])
    {
        $model = new AboutDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
    //添加广告
    public static function addAboutBanner ($data=[],$casesId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['aboutBannerPic']);
            $Banner = [
                'galleryId'=>$GalleryId,
                'aboutId' => $casesId,
                'aboutBannerPic' => $value['aboutBannerPic'],
                'sort'=>empty($value['sort']) ? 1 : $value['sort'],
            ];
            $model = new AboutBannerModel();
            $model->setDataByHumpArray($Banner)->save();
        }
    }

    //获取列表
    public static function getAboutList($data=[])
    {
        //语言筛选
        $name=[];
        $name2=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='about_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='about.is_on';
            $show=$data['isOn'];
        }
        $AboutLists = AboutModel::whereSiteId()
            ->where($name,$where)
            ->where($name2,$where)
            ->where($showName,$show)
            ->orderby('about.sort','DESC')
            ->leftjoin('about_description', 'about.about_id', '=', 'about_description.about_id')
            ->getHumpArray([
                'about.*',
                'about_description.*',
            ]);
        return $AboutLists;
    }

    //获取列表
    public static function getAboutTypeList()
    {
        $AboutLists = AboutModel::whereSiteId()
            ->getHumpArray([
                'about_id',
                'about_type'
            ]);
        return $AboutLists;
    }

    //获取菜单列表
    public static function getAboutMenuList($data=[])
    {
        //语言筛选
        $name=[];
        $name2=[];
        $where=[];
        $where1=[];
        if(isset($data['languageId'])){
            $name='about_description.language_id';
            $where=$data['languageId'];
        }else{
            $name2='about_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='about.is_on';
            $show=$data['isOn'];
        }
        $AboutLists = AboutModel::whereSiteId()
            ->where($name,$where)
            ->where($name2,$where1)
            ->where($showName,$show)
            ->orderby('about.sort','DESC')
            ->leftjoin('about_description', 'about.about_id', '=', 'about_description.about_id')
            ->getHumpArray([
                'about.about_id',
                'about.about_type',
                'about_description.about_title',
            ]);
        if(isset($AboutLists)){
            foreach ($AboutLists as $key=>$value) {
                $AboutLists[$key]['propType']='aboutType';
                $AboutLists[$key]['propId']='aboutId';
                $AboutLists[$key]['propTitle']='aboutTitle';
            }
        }
        return $AboutLists;
    }

    //获取住单条
    public static function getAboutOne($aboutId)
    {
        //图片处理路劲
        $About = AboutModel::where('about_id', $aboutId)->firstHump(['*']);
        if(isset($About)){
            $desc = self::getAboutDesc($About['aboutId']);
            if(isset($desc)){
                foreach ($desc as $key=>$value){
                    $About['aboutThumb']=empty($value['aboutThumb']) ? '' : $value['aboutThumb'];
                }
            }
            //获取广告图
            $banner = self::getBanner($About['aboutId']);
        }
        $About['aboutbanner']=empty($banner) ? [] : $banner;
        $About['lang'] = empty($desc) ? [] : $desc;
        return $About;
    }

    //获取广告图
    public static function getBanner($aboutId)
    {
        $image=AboutBannerModel::where('about_id',$aboutId)
            ->orderBy('sort','DESC')
            ->getHump(['about_banner_pic','sort']);
        return $image;
    }

    //查主表单条
    public static function getCateAbout($aboutCateId)
    {
        $About = AboutModel::where('about_cate_id', $aboutCateId)
            ->firstHump(['*']);
        return $About;
    }

    //获取详情的全部
    public static function getAboutDesc($aboutId)
    {
        $AboutDescLists = AboutDescModel::where('about_id',$aboutId)->getHump(['*']);
        return $AboutDescLists;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'isOn' => $data['isOn'],
            'sort' => $data['sort'],
            'aboutType' => empty($data['aboutType']) ? '' : $data['aboutType']
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['aboutThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['aboutThumb']);
            }
            self::editAbout($main,$data['aboutId']);
            foreach ($data['lang'] as $value){
                $desc=[
                    'aboutTitle' => $value['aboutTitle'],
                    'galleryId' => $GalleryId,
                    'aboutContent' => empty($value['aboutContent']) ? '' : $value['aboutContent'],
                    'aboutThumb' => empty($data['aboutThumb']) ? '' : $data['aboutThumb'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editAboutDesc($desc,$data['aboutId'],$value['languageId']);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //删除广告
            self::editAboutBanner($data['aboutId']);
            //广告
            if(isset($data['aboutbanner'])){
                self::addAboutBanner($data['aboutbanner'],$data['aboutId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editAbout($data=[],$aboutId)
    {
        AboutModel::where('about_id', $aboutId)->updateByHump($data);

    }
    //编辑详细表
    public static function editAboutDesc($data=[],$aboutId,$languageId)
    {
        AboutDescModel::where('about_id', $aboutId)->where('language_id',$languageId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$aboutId)
    {
        AboutModel::where('about_id', $aboutId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($aboutId)
    {
        \DB::beginTransaction();
        try{
            self::deleteAbout($aboutId);
            self::deleteAboutDesc($aboutId);
            self::deleteAboutBanner($aboutId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //图片删、该-广告
    public static function editAboutBanner($aboutId)
    {
        //查是否有图片
        $banner = self::getBanner($aboutId);
        if(isset($banner)){
            //有图片把它删除
            self::deleteAboutBanner($aboutId);
        }
    }
    //删除广告图
    public static function deleteAboutBanner($aboutId)
    {
        (new AboutBannerModel())->where('about_id', $aboutId)->delete();
    }
    //删除主
    public static function deleteAbout($aboutId)
    {
        (new AboutModel())->where('about_id', $aboutId)->delete();
    }
    //删除详
    public static function deleteAboutDesc($aboutId)
    {
        (new AboutDescModel())->where('about_id', $aboutId)->delete();
    }

    //=========================前端调用单条==============================

    //查单条
    public static function getAbout($aboutId,$languageId)
    {
        $about = AboutModel::whereSiteId()->where('about.about_id', $aboutId)
            ->where('about_description.language_id',$languageId)
            ->leftjoin('about_description', 'about.about_id', '=', 'about_description.about_id')
            ->firstHump([
                'about.*',
                'about_description.*',
            ]);
        $banner = self::getBanner($aboutId);
        $about['aboutbanner'] = empty($banner) ? [] : $banner;
        return $about;
    }

    //查单根据aboutId查aboutType
    public static function getAboutTypeName($aboutId)
    {
        $about = AboutModel::whereSiteId()->where('about_id', $aboutId)->firstHumpArray();
        return $about;
    }

    //根据aboutType查单条
    public static function getAboutType($aboutType,$languageId)
    {
        $about = AboutModel::whereSiteId()->where('about.about_type', $aboutType)
            ->where('about_description.language_id',$languageId)
            ->leftjoin('about_description', 'about.about_id', '=', 'about_description.about_id')
            ->firstHump([
                'about.*',
                'about_description.*',
            ]);
        $banner = self::getBanner($about['aboutId']);
        if($about){
            $about['aboutbanner'] = empty($banner) ? [] : $banner;
        }
        return $about;
    }

    //获取广告图
    public static function getAboutBanner($aboutId)
    {
//        $aboutBanner = AboutBannerModel::where('about_id',$aboutId)->getHumpArray(['*']);
        $aboutBanner = BannerModel::where('banner.label_id', $aboutId)
            ->leftJoin('banner_description','banner.banner_id','=','banner_description.banner_id')
            ->getHumpArray();

        foreach ($aboutBanner as $k => $v) {

            $aboutBanner[$k]['aboutBannerPic'] = $v['bannerImage'];
            unset($aboutBanner[$k]['bannerImage']);

        }

        return $aboutBanner;
    }

    //是否存在数据
    public static function getdefault()
    {
        $about = AboutModel::whereSiteId()->firstHump(['*']);
        return $about;
    }

    //语言--关于我们
    public static function getName($k,$languageId){
        if($k==0){
            if($languageId==1){
                $name='关于我们';
            }
            if($languageId==2){
                $name='about';
            }
        }
        if($k==1){
            if($languageId==1){
                $name='公司概念';
            }
            if($languageId==2){
                $name='Corporate concept';
            }
        }
        if($k==2){
            if($languageId==1){
                $name='企业文化';
            }
            if($languageId==2){
                $name='corporate culture';
            }
        }
        return $name;
    }

    //添加默认数据
    public static function addAboutDefault ($siteId,$languageId)
    {
        $arr=[];
        //获取站点的语言列表
        $lang=SiteLogic::getLangLists($siteId);
        if(isset($lang)){
            foreach ($lang as $key=>$value) {
                $arr[]=$value;
            }
        }
        $data=[];
        if($languageId==1){
            $res1['lang']=$arr;
            $res2['lang']=$arr;
            $res3['lang']=$arr;
            $data[]=$res1;
            $data[]=$res2;
            $data[]=$res3;
        }
        if(isset($data)){
            foreach ($data as $k=>$val){
                $num=$k+1;
                if($num==1){
                    $typeName = 'about';
                }
                if($num==2){
                    $typeName = 'concept';
                }
                if($num==3){
                    $typeName = 'culture';
                }
                $mian=[
                    'aboutType'=>$typeName
                ];
                $aboutId=self::addAbout($mian);
                if(!empty($aboutId && $val['lang'])){
                    foreach ($val['lang'] as $k2=>$item) {
                        $item['aboutId']=$aboutId;

                        $item['aboutContent']='';
                        //获取名称
                        $name = self::getName($k,$item['languageId']);
                        $item['aboutTitle']=$name;
                        self::addAboutDesc($item);
                    }
                }
            }
        }

    }

    //--------图库------------获取图片使用------------------
    public static function getImage()
    {
        $AboutLists = AboutModel::whereSiteId()->getHumpArray(['about_id']);
        $arr=[];
        foreach ($AboutLists as $key=>$value){
            $image = self::getAboutDescImage($value['aboutId']);
            $AboutLists[$key]['image']=$image['aboutThumb'];
            $banner = self::getAboutBannerImg($value['aboutId']);
            $arr[] = $banner;
        }
        $res = [];
        foreach ($arr as $k=>$val){
            if($val){
                foreach ($val as $k1=>$item){
                    $change['aboutBannerId']=$item['aboutBannerId'];
                    $change['image']=$item['aboutBannerPic'];
                    $res[]=$change;
                }
            }
        }
        $res1 = array_merge($AboutLists,$res);
        //删除空图片的数组
        $res2=self::delImageArray($res1);
        //var_dump($res2);
        return $res2;
    }
    public static function getAboutDescImage($aboutId)
    {
        $image = AboutDescModel::where('about_id',$aboutId)->firstHumpArray(['about_thumb',]);
        return $image;
    }
    public static function getAboutBannerImg($aboutId)
    {
        $banner = AboutBannerModel::where('about_id',$aboutId)->getHumpArray(['about_banner_id','about_banner_pic']);
        return $banner;
    }
    public static function delImageArray($arr)
    {
        $res=[];
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                if (empty($v['image'])) unset($arr[$k]);
                else($res[]=$v);
            }
        }
        $image = self::more_array_unique($res);
        return $image;
    }
    public static function delSameImage($res,$where=null)
    {
        $arr=[];
        foreach ($res as $key=>$value){

        }
        var_dump($arr);
    }
    public static function more_array_unique($arr=array()){
        $res=[];
        foreach($arr[0] as $k => $v){
            $arr_inner_key[]= $k;   //先把二维数组中的内层数组的键值记录在在一维数组中
        }
        foreach ($arr as $k => $v){
            //$v =join(",",$v);    //降维 用implode()也行
            $temp[$k] =$v['image'];      //保留原来的键值 $temp[]即为不保留原来键值
        }
        $temp =array_unique($temp);//去重：去掉重复的字符串
        foreach ($arr as $key=>$value){

        }

        return ;
    }

    //清空详情图片
    public static function ClearImage($galleryId)
    {
        $data['aboutThumb']='';
        $data['galleryId']=0;
        AboutDescModel::where('gallery_id', $galleryId)->updateByHump($data);
        return;
    }

    //删除图片
    public static function deleteBannerImage($galleryId)
    {
        (new AboutBannerModel())->where('gallery_id', $galleryId)->delete();
    }

    //测试
    public static function getAboutTest()
    {
        $languageId=2;
        $about = AboutModel::whereSiteId()->where('about_id',8)->firstHump();
        $about->aboutbanner=$about->banner()->getHumpArray(['sort','aboutBannerPic']);
        $desc = $about->desc();
        if($languageId==1){
            $desc = $desc->where('language_id',$languageId);
        }
        $about->desc=$desc->getHumpArray();
        return $about;
    }
    //前端
    public static function getAboutTest2()
    {
        $languageId=1;
        $languageIdArray=[$languageId];
        $about = AboutModel::whereSiteId()->where('about_id',8)->firstHump();
        $desc=$about->desc()->whereIn('language_id',$languageIdArray)->firstHumpArray();
        $arr = $about->toArray();
        $res = array_merge($arr,$desc);
        return $res;
    }


}
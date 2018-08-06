<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Cases;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Cases\CasesBannerModel;
use App\Model\Cases\CasesModel;
use App\Model\Cases\CasesDescModel;
use App\Model\Cases\CasesimageModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class CasesLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'casesCateId' => $data['casesCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'isOn' => $data['isOn'],
            'tableId'=>$tableId,

        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['casesImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['casesImage']);
            }
            $casesId=self::addCases($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'casesId' => $casesId,
                    'galleryId'=>$GalleryId,
                    'casesTitle' => $value['casesTitle'],
                    'casesContent' => empty($value['casesContent']) ? '' : $value['casesContent'],
                    'casesDesc' => empty($value['casesDesc']) ? '' : $value['casesDesc'],
                    'casesImage' => empty($data['casesImage']) ? '' : $data['casesImage'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addCasesDesc($desc);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //广告图
            if(isset($data['casesbanner'])){
                self::addCasesBanner($data['casesbanner'],$casesId);
            }
            //添加图片
            if(isset($data['lang'])){
                foreach ($data['lang'] as $key=>$value){
                    if(isset($value['photos'])){
                        self::addCasesimage($value['photos'],$casesId);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addCases ($data=[])
    {
        //检查排序
        self::Sort($data['casesCateId'],$data['sort'],$data['tableId']);
        $model = new CasesModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //查排序是否唯一
    public static function Sort ($casesCateId,$sort,$tableId)
    {
        $res = CasesModel::whereSiteId()->where('table_id',$tableId)->where('cases_cate_id',$casesCateId)->where('sort',$sort)->firstHump(['*']);
        $sort = CasesModel::whereSiteId()->where('table_id',$tableId)->where('cases_cate_id',$casesCateId)->orderby('sort','DESC')->firstHump(['sort']);
        if(!empty($res)){
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'CASES_SORT');
        }
    }

    //添加详细表
    public static function addCasesDesc ($data=[])
    {
        $model = new CasesDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //添加广告
    public static function addCasesBanner ($data=[],$casesId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['casesBannerPic']);
            $casesBanner = [
                'galleryId'=>$GalleryId,
                'casesId' => $casesId,
                'casesBannerPic' => $value['casesBannerPic'],
                'sort'=>empty($value['sort']) ? 1 : $value['sort'],
            ];
            $model = new CasesBannerModel();
            $model->setDataByHumpArray($casesBanner)->save();
        }
    }

    //添加图片表
    public static function addCasesimage ($data=[],$casesId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['casesImagePic']);
            $Casesimage = [
                'galleryId'=>$GalleryId,
                'casesId' => $casesId,
                'casesImagePic' => $value['casesImagePic'],
                'casesImageDesc' => empty($value['casesImageDesc']) ? '' : $value['casesImageDesc'],
                'languageId'=>$value['languageId'],
            ];
            $model = new CasesimageModel();
            $model->setDataByHumpArray($Casesimage)->save();
        }
    }

    //获取列表
    public static function getCasesList($data)
    {
        $tableId=$data['tableId'];
        $name=[];
        $where=[];
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='cases.is_on';
            $show=$data['isOn'];
        }
        if(isset($data['languageId'])){
            $name='cases_description.language_id';
            $where=$data['languageId'];
        }
        if (isset($data['casesTitle'])) {
            $CasesTitle = '%' . $data['casesTitle'] . '%';
        } else {
            $CasesTitle = '%';
        }
        if(isset($data['casesCateId'])){
            $name2='cases.cases_cate_id';
            $cateId = CasesCateLogic::getCateId($data['casesCateId'],$tableId);
            $CasesLists = CasesModel::whereSiteId()
                ->where('cases.table_id',$tableId)
                ->where($name,$where)
                ->where($showName,$show)
                ->whereIn($name2,$cateId)
                ->where('cases_description.cases_title', 'like', $CasesTitle)
                ->orderby('cases.sort','DESC')
                ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
                ->select([
                    'cases.*',
                    'cases_description.*',
                ])
            ;
        }else{
            $CasesLists = CasesModel::whereSiteId()
                ->where('cases.table_id',$tableId)
                ->where($name,$where)
                ->where($showName,$show)
                ->where('cases_description.cases_title', 'like', $CasesTitle)
                ->orderby('cases.sort','DESC')
                ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
                ->select([
                    'cases.*',
                    'cases_description.*',
                ])
            ;
        }
        $res = $CasesLists->getDdvPageHumpArray(true);
        if(isset($res)){
            foreach ($res['lists'] as $key=>$value){
                $name=CasesCateLogic::getCasesCateName($value['casesCateId'],$value['languageId']);
                $res['lists'][$key]['casesCateTitle']=empty($name) ? '' : $name;
            }
        }
        return $res;
    }

    //获取住单条
    public static function getCasesOne($casesId)
    {
        $Cases = CasesModel::whereSiteId()
            ->where('cases_id', $casesId)
            ->firstHump(['*']);
        if(isset($Cases)){
            $CasesDesc=self::getCasesDesc($Cases['casesId']);
            if(isset($CasesDesc)){
                foreach ($CasesDesc as $key=>$value){
                    $Cases['casesImage']=empty($value['casesImage']) ? '' : $value['casesImage'];
                    $image = self::getImage($Cases['casesId'],$value['languageId']);
                    $value['photos']=empty($image) ? [] : $image;
                }
            }
            //获取广告图
            $banner = self::getBanner($casesId);
        }
        $Cases['casesbanner']=empty($banner) ? [] : $banner;
        $Cases['lang']=empty($CasesDesc) ? [] : $CasesDesc;
        return $Cases;
    }

    //获取详情全部
    public static function getCasesDesc($casesId)
    {
        $CasesDeac = CasesDescModel::where('cases_id', $casesId)
            ->getHump(['*']);
        return $CasesDeac;
    }

    //查主表单条
    public static function getCateCases($casesCateId)
    {
        $Cases = CasesModel::where('cases_cate_id', $casesCateId)
            ->firstHump(['*']);
        return $Cases;
    }

    //获取广告图
    public static function getBanner($casesId)
    {
        $image=CasesBannerModel::where('cases_id',$casesId)
            ->orderBy('sort','DESC')
            ->getHump(['cases_banner_pic','sort']);
        return $image;
    }

    //获取全部图片
    public static function getAllBanner($casesId)
    {
        $image=CasesBannerModel::where('cases_id',$casesId)->orderBy('sort','DESC')->getHump(['*']);
        return $image;
    }

    //获取图片
    public static function getImage($casesId,$languageId)
    {
        $image=CasesimageModel::where('cases_id',$casesId)
            ->where('language_id',$languageId)
            ->getHump(['cases_image_pic','cases_image_desc','language_id']);
        return $image;
    }
    //获取全部图片
    public static function getAllImage($casesId)
    {
        $image=CasesimageModel::where('cases_id',$casesId)->getHump(['*']);
        return $image;
    }

    //======================单条查全部----修改全部======================================
    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'casesCateId' => $data['casesCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'isOn' => $data['isOn'],
            //'tableId'=>$tableId,
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['casesImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['casesImage']);
            }
            $casesId=$data['casesId'];
            self::editCases($main,$casesId,$data['tableId']);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'casesTitle' => $value['casesTitle'],
                    'casesContent' => empty($value['casesContent']) ? '' : $value['casesContent'],
                    'casesDesc' => empty($value['casesDesc']) ? '' : $value['casesDesc'],
                    'casesImage' => empty($data['casesImage']) ? '' : $data['casesImage'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editCasesDesc($desc,$casesId,$value['languageId']);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //删除广告
            self::editCasesBanner($casesId);
            //广告
            if(isset($data['casesbanner'])){
                self::addCasesBanner($data['casesbanner'],$casesId);
            }
            //删除图片
            self::editCasesimage($casesId);
            //添加图片
            if(isset($data['lang'])){
                foreach ($data['lang'] as $key=>$value){
                    if(isset($value['photos'])){
                        self::addCasesimage($value['photos'],$casesId);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }


    //编辑主表
    public static function editCases($data=[],$casesId,$tableId)
    {
        //检查排序
        self::SortTwo($data['casesCateId'],$data['sort'],$casesId,$tableId);
        CasesModel::where('cases_id', $casesId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$casesId)
    {
        CasesModel::where('cases_id', $casesId)->updateByHump($data);
    }

    //查排序是否唯一
    public static function SortTwo ($casesCateId,$sort,$casesId,$tableId)
    {
        $res = CasesModel::whereSiteId()->where('table_id',$tableId)->where('cases_cate_id',$casesCateId)->where('cases_id','<>',$casesId)->where('sort',$sort)->firstHump(['*']);
        if(!empty($res)){
            $sort = CasesModel::whereSiteId()->where('table_id',$tableId)->where('cases_cate_id',$casesCateId)->orderby('sort','DESC')->firstHump(['sort']);
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'CASES_SORT');
        }
    }

    //编辑详细表
    public static function editCasesDesc($data=[],$casesId,$languageId)
    {
        CasesDescModel::where('cases_id', $casesId)->where('language_id',$languageId)->updateByHump($data);
    }
    //===========================================================================


    //删除事务
    public static function delAffair($casesId)
    {
        \DB::beginTransaction();
        try{
            self::deleteCases($casesId);
            self::deleteCasesDesc($casesId);
            self::deleteCasesImage($casesId);
            self::deleteCasesBanner($casesId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }


    //删除主
    public static function deleteCases($casesId)
    {
        (new CasesModel())->where('cases_id', $casesId)->delete();
    }
    //删除详
    public static function deleteCasesDesc($casesId)
    {
        (new CasesDescModel())->where('cases_id', $casesId)->delete();
    }

    //图片删、该-广告
    public static function editCasesBanner($casesId)
    {
        //查是否有图片
        $banner = self::getAllBanner($casesId);
        if(isset($banner)){
            //有图片把它删除
            self::deleteCasesBanner($casesId);
        }
    }

    //删除广告图
    public static function deleteCasesBanner($casesId)
    {
        (new CasesBannerModel())->where('cases_id', $casesId)->delete();
    }

    //图片删、该
    public static function editCasesimage($casesId)
    {
        //查是否有图片
        $image = self::getAllImage($casesId);
        if(isset($image)){
            //有图片把它删除
            self::deleteCasesImage($casesId);
        }
    }

    //删除图片
    public static function deleteCasesImage($casesId)
    {
        (new CasesimageModel())->where('cases_id', $casesId)->delete();
    }

    //=========================前端调用单条===============================

    //查单条
    public static function getCases($casesId,$languageId)
    {
        $cases = CasesModel::where('cases.cases_id', $casesId)
            ->where('cases_description.language_id',$languageId)
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->leftjoin('cases_category_description', 'cases.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->firstHump([
                'cases.*',
                'cases_description.*',
                'cases_category_description.cases_cate_title',
            ]);
        if(isset($cases)){
            //广告图
            $banner = self::getBanner($cases['casesId']);
            $cases['casesbanner']=empty($banner) ? [] : $banner;
            //图片
            $image = self::getImage($cases['casesId'],$languageId);
            $cases['photos']=empty($image) ? [] : $image;
            //点击量
            $hit['casesHit']=$cases['casesHit']+1;
            self::Click($hit,$cases['casesId']);
        }

        return $cases;
    }
    //点击量
    public static function Click($data,$casesId){
        CasesModel::where('cases_id', $casesId)->updateByHump($data);
    }
    //获取上一条数据
    public static function getLast($casesCateId,$languageId,$sort)
    {
        $last= CasesModel::whereSiteId()
            ->where('cases.cases_cate_id', $casesCateId)
            ->where('cases_description.language_id',$languageId)
            ->where('cases.sort','>',$sort)
            ->orderby('cases.sort','ASC')
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->firstHump([
                'cases.cases_id',
                'cases.created_at',
                'cases_description.cases_title',
                'cases_description.cases_image',
                'cases_description.cases_desc',
            ]);
        return $last;
    }
    //获取下一条数据
    public static function getNext($casesCateId,$languageId,$sort)
    {
        $next= CasesModel::whereSiteId()
            ->where('cases.cases_cate_id', $casesCateId)
            ->where('cases_description.language_id',$languageId)
            ->where('cases.sort','<',$sort)
            ->orderby('cases.sort','DESC')
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->firstHump([
                'cases.cases_id',
                'cases.created_at',
                'cases_description.cases_title',
                'cases_description.cases_image',
                'cases_description.cases_desc',
            ]);

        return $next;
    }
    //获取推荐
    public static function getRecommend($languageId,$number,$tableId)
    {
        $cases= CasesModel::whereSiteId()
            ->where('cases.table_id',$tableId)
            ->where('cases.recommend', 1)
            ->where('cases_description.language_id',$languageId)
            ->limit($number)
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->getHumpArray([
                'cases.*',
                'cases_description.*',
            ]);

        foreach ($cases as $k => $v) {
            //图片
            $image = self::getImage($cases[$k]['casesId'],$languageId);
            $cases[$k]['photos']=empty($image) ? [] : $image;
        }

        return $cases;
    }

    //获取导航用
    public static function getMenuName($casesId,$languageId)
    {
        $model = new CasesModel();
        $cases = $model->whereSiteId()->where('cases.cases_id', $casesId)
            ->where('cases_description.language_id',$languageId)
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->firstHumpArray(['cases.*','cases_description.*']);
        if(!empty($cases)){
            $arr=[];
            $arr[]=$cases;
            //获取父亲
            $res = CasesCateLogic::getCasesCateParents($cases['casesCateId'],$languageId);
            $arr2=[];
            if(!empty($res)){
                $arr2=array_merge($res,$arr);
            }
            return $arr2;
        }
        return;
    }
    //计算
    public static function casesCount($cateId){
        $model = new CasesModel();
        $count = $model->whereSiteId()
            ->whereIn('cases_cate_id', $cateId)
            ->count();
        return $count;
    }

    //获取导航banner使用
    public static function getMenuBanner($casesId,$languageId)
    {
        $model = new CasesModel();
        $product = $model->whereSiteId()->where('cases.cases_id', $casesId)
            ->where('cases_description.language_id',$languageId)
            ->leftjoin('cases_description', 'cases.cases_id', '=', 'cases_description.cases_id')
            ->firstHumpArray(['cases.*','cases_description.*']);

        return $product;
    }

}
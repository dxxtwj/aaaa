<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Works;

use App\Logic\Common\TreeLogic;
use App\Http\Middleware\SiteId;
use App\Logic\Gallery\GalleryLogic;
use App\Logic\Menu\MenuLogic;
use App\Model\Works\WorksCateDescModel;
use App\Model\Works\WorksCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class WorksCateLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $GalleryId=0;
        if(!empty($data['worksThumb'])){
            $GalleryId = GalleryLogic::getGalleryId($data['worksThumb']);
        }
        $startTime = strtotime($data['startTime'])-28800;
        $endTime = strtotime($data['endTime'])-28800;
        $main=[
            'galleryId'=>$GalleryId,
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort'],
            'startTime' => empty($startTime) ? 0 : $startTime,
            'endTime' => empty($endTime) ? 0 : $endTime,
            'worksThumb'=>empty($data['worksThumb']) ? '' : $data['worksThumb']
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $worksCateId=self::addWorksCate($main);
            foreach ($data['lang'] as $key=>$value ){
                $desc=[
                    'worksCateId' => $worksCateId,
                    'worksCateTitle' => $value['worksCateTitle'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addWorksCateDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addWorksCate ($data=[])
    {
        $model = new WorksCateModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addWorksCateDesc ($data=[])
    {
        $model = new WorksCateDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取新闻分类列表
    public static function getWorksCateLists($data)
    {
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='works_category.is_on';
            $show=$data['isOn'];
        }
        $languageId = SiteId::getLanguageId();
        if(isset($data['languageId'])){
            $languageId=$data['languageId'];
        }
        $worksLists = WorksCateModel::whereSiteId()
            ->where($showName,$show)
            ->where('works_category_description.language_id',$languageId)
            ->leftjoin('works_category_description', 'works_category.works_cate_id', '=', 'works_category_description.works_cate_id')
            ->getHumpArray([
                'works_category.*',
                'works_category_description.*',
            ]);
        $cases = TreeLogic::WorksTree($worksLists);
        return $cases;
    }

    //获取主单条
    public static function getWorksCateOne($worksCateId)
    {
        $works = WorksCateModel::where('works_cate_id', $worksCateId)
            ->firstHump(['*']);
        if(isset($works)){
            $worksCateDesc = self::getWorksCateDesc($works['worksCateId']);
            $works['lang']=empty($worksCateDesc) ? [] : $worksCateDesc;
        }
        return $works;
    }

    //获取详情全部
    public static function getWorksCateDesc($worksCateId)
    {
        $worksCateDesc = WorksCateDescModel::where('works_cate_id', $worksCateId)->getHump(['*']);
        return $worksCateDesc;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $GalleryId=0;
        if(!empty($data['worksThumb'])){
            $GalleryId = GalleryLogic::getGalleryId($data['worksThumb']);
        }
        $startTime = strtotime($data['startTime'])-28800;
        $endTime = strtotime($data['endTime'])-28800;
        $main=[
            'galleryId'=>$GalleryId,
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort'],
            'startTime' => empty($startTime) ? 0 : $startTime,
            'endTime' => empty($endTime) ? 0 : $endTime,
            'worksThumb'=>empty($data['worksThumb']) ? '' : $data['worksThumb']
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $worksCateId=$data['worksCateId'];
            self::editWorksCate($main,$worksCateId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'worksCateId' => $worksCateId,
                    'worksCateTitle' => $value['worksCateTitle'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editWorksCateDesc($desc,$worksCateId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return true;
    }

    //编辑主表
    public static function editWorksCate($data=[],$worksCateId)
    {
        WorksCateModel::where('works_cate_id', $worksCateId)->updateByHump($data);
    }

    //编辑详细表
    public static function editWorksCateDesc($data=[],$worksCateId,$languageId)
    {
        WorksCateDescModel::where('works_cate_id', $worksCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //获取上一级ID
    public static function getWorksCateId($worksCateId)
    {
        $works = WorksCateModel::where('works_cate_id', $worksCateId)->firstHump(['pid']);
        return $works;
    }

    //获取下一级
    public static function getChildId($worksCateId)
    {
        $cases = WorksCateModel::where('pid', $worksCateId)->firstHump(['works_cate_id']);
        return $cases;
    }

    //删除事务
    public static function delAffair($worksCateId)
    {
        \DB::beginTransaction();
        try{
            self::deleteWorksCate($worksCateId);
            self::deleteWorksCateDesc($worksCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return true;
    }

    //删除主
    public static function deleteWorksCate($worksCateId)
    {
        $cases = WorksLogic::getCateWorks($worksCateId);
        if (!empty($cases)){
            throw new RJsonError('该类下还有数据', 'DELETE_WORKS');
        }
        $cate = self::getChildId($worksCateId);
        if (isset($cate)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_WORKS_CATE');
        }
        (new WorksCateModel())->where('works_cate_id', $worksCateId)->delete();
    }
    //删除详
    public static function deleteWorksCateDesc($worksCateId)
    {
        (new WorksCateDescModel())->where('works_cate_id', $worksCateId)->delete();
    }

    //获取详情全部
    public static function getWorksCateName($worksCateId,$languageId)
    {
        $worksCateDesc = WorksCateDescModel::where('works_cate_id', $worksCateId)->where('language_id',$languageId)->firstHump(['works_cate_title']);
        return $worksCateDesc->worksCateTitle;
    }

    //获取类下的所有子类ID--可以-还没应用
    public static function getCateId($worksCateId)
    {
        $data = WorksCateModel::whereSiteId()->where('pid',$worksCateId)->getHumpArray(['pid','worksCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['worksCateId'];
            }
        }
        $arr2 = self::Workstree($arr);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $worksCateId=(int) $worksCateId;
        array_push($res,$worksCateId);
        return $res;
    }

    public static function Workstree($worksCateId) {
        $arr = array();
        $arr3=[];
        $data = WorksCateModel::whereSiteId()->whereIn('pid',$worksCateId)->getHumpArray(['pid','worksCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['worksCateId'];
                $arr2 =self::Workstree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }


    //=========================前端调用单条==============================

    //查单条
    public static function getWorksCate($worksCateId,$languageId)
    {
        $works = WorksCateModel::whereSiteId()
            ->where('works_category.works_cate_id', $worksCateId)
            ->where('works_category_description.language_id',$languageId)
            ->leftjoin('works_category_description', 'works_category.works_cate_id', '=', 'works_category_description.works_cate_id')
            ->firstHumpArray([
                'works_category.*',
                'works_category_description.*',
            ]);
        return $works;
    }

    //获取面包屑
    public static function getCateName($worksCateId,$languageId){
        $works = WorksCateModel::whereSiteId()
            ->where('works_category.works_cate_id', $worksCateId)
            ->where('works_category_description.language_id',$languageId)
            ->leftjoin('works_category_description', 'works_category.works_cate_id', '=', 'works_category_description.works_cate_id')
            ->getHumpArray([
                'works_category.*',
                'works_category_description.*',
            ]);
        $arr = TreeLogic::getWorksParents($works,$worksCateId);
        return $arr;
    }

}
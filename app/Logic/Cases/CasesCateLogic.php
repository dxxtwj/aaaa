<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Cases;

use App\Logic\Common\TreeLogic;
use App\Logic\Gallery\GalleryLogic;
use App\Model\Cases\CasesCateModel;
use App\Model\Cases\CasesCateDescModel;
use App\Http\Middleware\SiteId;
use App\Logic\Menu\MenuLogic;
use App\Logic\Cases\CasesLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class CasesCateLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort'],
            'tableId'=>$tableId

        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['casesCateImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['casesCateImage']);
            }
            $casesCateId=self::addCasesCate($main);
            foreach ($data['lang'] as $key=>$value ){
                $desc=[
                    'casesCateId' => $casesCateId,
                    'galleryId'=>$GalleryId,
                    'casesCateTitle' => $value['casesCateTitle'],
                    'casesCateImage' => empty($data['casesCateImage']) ? '' : $data['casesCateImage'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addCasesCateDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addCasesCate ($data=[])
    {
        $model = new CasesCateModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addCasesCateDesc ($data=[])
    {
        $model = new CasesCateDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }


    //获取新闻分类列表
    public static function getCasesCateList($data=[])
    {
        $tableId=$data['tableId'];
        $name=[];
        $where=[];
        $name1=[];
        $where1=[];
        if(isset($data['languageId'])){
            $name='cases_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1 ='cases_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='cases_category.is_on';
            $show=$data['isOn'];
        }
        $casesLists = CasesCateModel::whereSiteId()
            ->where('cases_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->where($showName,$show)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.cases_cate_id',
                'cases_category.pid',
                'cases_category.sort',
                'cases_category.site_id',
                'cases_category.is_on',
                'cases_category_description.cases_cate_id',
                'cases_category_description.language_id',
                'cases_category_description.cases_cate_title',
                'cases_category_description.cases_cate_image',
                'cases_category_description.site_title',
                'cases_category_description.site_keywords',
                'cases_category_description.site_description',

            ]);
        $cases = TreeLogic::Casestree($casesLists);
        return $cases;
    }

    //获取新闻分类列表
    public static function getCateLists($data=[])
    {
        $tableId=$data['tableId'];
        $name=[];
        $where=[];
        $name1=[];
        $where1=[];
        if(isset($data['languageId'])){
            $name='cases_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1 ='cases_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='cases_category.is_on';
            $show=$data['isOn'];
        }
        $casesLists = CasesCateModel::whereSiteId()
            ->where('cases_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->where($showName,$show)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.cases_cate_id',
                'cases_category.pid',
                'cases_category_description.cases_cate_title',
            ]);
        if(isset($casesLists)){
            foreach ($casesLists as $key=>$value) {
                $casesLists[$key]['propId']='casesCateId';
                $casesLists[$key]['propTitle']='casesCateTitle';
            }
        }
        $cases = TreeLogic::Casestree($casesLists);
        return $cases;
    }

    //获取主单条
    public static function getCasesCateOne($casesCateId)
    {
        $cases = CasesCateModel::where('cases_cate_id', $casesCateId)
            ->firstHump(['*']);
        if(isset($cases)){
            $casesCateDesc = self::getCasesCateDesc($cases['casesCateId']);
            if(isset($casesCateDesc)){
                foreach ($casesCateDesc as $key=>$value){
                    $cases['casesCateImage']=empty($value['casesCateImage']) ? '' : $value['casesCateImage'];
                }
            }
        }
        $cases['lang']=empty($casesCateDesc) ? [] : $casesCateDesc;
        return $cases;
    }

    //获取详情全部
    public static function getCasesCateDesc($casesCateId)
    {
        $casesCateDesc = CasesCateDescModel::where('cases_cate_id', $casesCateId)->getHump(['*']);
        return $casesCateDesc;
    }

    //获取详情全部
    public static function getCasesCateName($casesCateId,$languageId)
    {
        $casesCateDesc = CasesCateDescModel::where('cases_cate_id', $casesCateId)->where('language_id',$languageId)->firstHump(['cases_cate_title']);
        return $casesCateDesc->casesCateTitle;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'casesCateId' => $data['casesCateId'],
            'isOn' => $data['isOn'],
            'sort'=>$data['sort']
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['casesCateImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['casesCateImage']);
            }
            $casesCateId=$data['casesCateId'];
            self::editCasesCate($main,$casesCateId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'casesCateTitle' => $value['casesCateTitle'],
                    'casesCateImage' => empty($data['casesCateImage']) ? '' : $data['casesCateImage'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editCasesCateDesc($desc,$casesCateId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editCasesCate($data=[],$casesCateId)
    {
        CasesCateModel::where('cases_cate_id', $casesCateId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$casesCateId)
    {
        CasesCateModel::where('cases_cate_id', $casesCateId)->updateByHump($data);

    }

    //编辑详细表
    public static function editCasesCateDesc($data=[],$casesCateId,$languageId)
    {
        CasesCateDescModel::where('cases_cate_id', $casesCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($casesCateId)
    {
        \DB::beginTransaction();
        try{
            self::deleteCasesCate($casesCateId);
            self::deleteCasesCateDesc($casesCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteCasesCate($casesCateId)
    {
        $cases = CasesLogic::getCateCases($casesCateId);
        if (!empty($cases)){
            throw new RJsonError('该类下还有数据', 'DELETE_CASESCATE');
        }
        $cate = self::getChildId($casesCateId);
        if (isset($cate)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_CASES_CATE');
        }
        (new CasesCateModel())->where('cases_cate_id', $casesCateId)->delete();
    }
    //删除详
    public static function deleteCasesCateDesc($casesCateId)
    {
        (new CasesCateDescModel())->where('cases_cate_id', $casesCateId)->delete();
    }


    //获取上一级ID
    public static function getCasesCateId($casesCateId)
    {
        $Cases = CasesCateModel::where('cases_category.cases_cate_id', $casesCateId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->firstHump([
                'cases_category.pid'
            ]);
        return $Cases;

    }

    //获取下一级
    public static function getChildId($casesCateId)
    {
        $cases = CasesCateModel::where('cases_category.pid', $casesCateId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->firstHump([
                'cases_category.cases_cate_id'
            ]);
        return $cases;

    }

    //获取子类
    public static function casesCate($casesCateId,$tableId){
        $arr=[];
        $casesLists = CasesCateModel::whereSiteId()->where('table_id',$tableId)->getHumpArray(['pid','casesCateId']);
        $casesCate = TreeLogic::CasesSubs($casesLists,$casesCateId);
        if(!empty($casesCate)){
            foreach ($casesCate as $key=>$value){
                $arr[]=$value['casesCateId'];
                if(!empty($value['child'])){
                    foreach ($value['child'] as $val) {
                        $arr[]=$val['casesCateId'];
                        if($value['child']){
                            foreach ($val['child'] as $v) {
                                $arr[]=$v['casesCateId'];
                            }
                        }
                    }
                }
            }
        }
        $casesCateId=(int) $casesCateId;
        array_push($arr,$casesCateId);
        return $arr;
    }

    //获取类下的所有子类ID--可以-还没应用
    public static function getCateId($casesCateId,$tableId)
    {
        $data = CasesCateModel::whereSiteId()->where('table_id',$tableId)->where('pid',$casesCateId)->getHumpArray(['pid','casesCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['casesCateId'];
            }
        }
        $arr2 = self::Casestree($arr,$tableId);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $casesCateId=(int) $casesCateId;
        array_push($res,$casesCateId);
        return $res;
    }

    public static function Casestree($casesCateId,$tableId) {
        $arr = array();
        $arr3=[];
        $data = CasesCateModel::whereSiteId()->where('table_id',$tableId)->whereIn('pid',$casesCateId)->getHumpArray(['pid','casesCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['casesCateId'];
                $arr2 =self::Casestree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }


    //=========================前端调用单条==============================

    //查单条
    public static function getCasesCate($casesCateId,$languageId)
    {
        $cases = CasesCateModel::where('cases_category.cases_cate_id', $casesCateId)
            ->where('cases_category_description.language_id',$languageId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->firstHumpArray([
                'cases_category.*',
                'cases_category_description.*',
            ]);

        return $cases;
    }

    //获取导航名称用--面包宵
    public static function getCasesCateParents($casesCateId,$languageId)
    {
        $model = new CasesCateModel();
        $cases = $model->whereSiteId()
            ->where('cases_category_description.language_id',$languageId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.*',
                'cases_category_description.*',
            ]);
        $res = TreeLogic::getCasesParents($cases,$casesCateId);
        if(!empty($res)){
            $arr=[];
            foreach ($res as $value) {
                if($value['pid']==0){
                    $res2 = MenuLogic::getMenuNameByClassId($value['casesCateId'],$languageId,3);
                    if(empty($res2)){
                        //如果不存在，去找同级的导航
                        $res2 = self::getMenuNameByBrother($value['tableId'],$languageId);
                        $check=$res2['check'];
                        unset($res2['check']);
                    }else{
                        //检测是否有相同页面的
                        $check=MenuLogic::getMenuCheckByClassId($value['casesCateId'],$languageId,3);
                    }
                }
                if(!empty($check)){
                    if($check > 1){
                        $arr[]=$value;
                    }else{
                        if($value['pid']!=0){
                            $arr[]=$value;
                        }
                    }
                }else{
                    $arr[]=$value;
                }

            }
            if(empty($res2)){
                $cases = self::getCasesCate($casesCateId,$languageId);
                $urlName='case';
                $menu = MenuLogic::getMenuIdByUrl($urlName,$cases['tableId'],$languageId);
                $res2[] = $menu;
            }
            $arr2 = array_merge($res2,$arr);
            return $arr2;
        }else{
            return;
        }
    }
    public static function getMenuNameByBrother($tableId,$languageId)
    {
        $res = self::getParents($tableId,$languageId);
        foreach ($res as $value){
            $res2 = MenuLogic::getMenuByClassId($value['casesCateId'],$languageId,3);
            $res2['check']=2;
            return $res2;
        }
        return;
    }

    //获取类下所有子类，并统计每个类下案例的条数
    public static function getCasesCateKids($casesCateId,$tableId,$languageId)
    {
        $model = new CasesCateModel();
        $cases = $model->whereSiteId()
            ->where('cases_category.table_id',$tableId)
            ->where('cases_category.pid',$casesCateId)
            ->where('cases_category_description.language_id',$languageId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.*',
                'cases_category_description.*',
            ]);
        if(!empty($cases)){
            foreach ($cases as $key=>$value){
                $cateId=self::getCateId($value['casesCateId'],$tableId);
                $count = CasesLogic::casesCount($cateId);
                $cases[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['casesCateId'],$tableId);
                if(!empty($res)){
                    $cases[$key]['kids']=true;
                }else{
                    $cases[$key]['kids']=false;
                }
            }
        }
        return $cases;
    }

    //获取父级，并统计每个类下案例的条数
    public static function getParents($tableId,$languageId){
        $model = new CasesCateModel();
        $cases = $model->whereSiteId()
            ->where('cases_category.table_id',$tableId)
            ->where('cases_category.pid',0)
            ->where('cases_category_description.language_id',$languageId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.*',
                'cases_category_description.*',
            ]);
        if(!empty($cases)){
            foreach ($cases as $key=>$value){
                $cateId=self::getCateId($value['casesCateId'],$tableId);
                $count = CasesLogic::casesCount($cateId);
                $cases[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['casesCateId'],$tableId);
                if(!empty($res)){
                    $cases[$key]['kids']=true;
                }else{
                    $cases[$key]['kids']=false;
                }

            }
        }
        return $cases;
    }

    //menu->banner
    public static function getCasesCateParent($casesCateId,$languageId)
    {
        $model = new CasesCateModel();
        $cases = $model->whereSiteId()
            ->where('cases_category_description.language_id',$languageId)
            ->leftjoin('cases_category_description', 'cases_category.cases_cate_id', '=', 'cases_category_description.cases_cate_id')
            ->getHumpArray([
                'cases_category.*',
                'cases_category_description.*',
            ]);
        $res2 = TreeLogic::getCasesParents($cases,$casesCateId);
        if(!empty($res2)){
            foreach ($res2 as $key=>$value){
                $arr = MenuLogic::getMenuBannerByClassId($value['casesCateId'],$languageId,3);
                if(!empty($arr)){
                    $arr2 = MenuLogic::getMenuBanner($arr['menuId'],$languageId);
                    if(!empty($arr2)){
                        return $arr2;
                    }
                }
            }
        }
    }


}
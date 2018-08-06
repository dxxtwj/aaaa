<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\News;

use App\Logic\Common\TreeLogic;
use App\Logic\Menu\MenuLogic;
use App\Model\News\NewsCateModel;
use App\Model\News\NewsCateDescModel;
use App\Http\Middleware\SiteId;
use App\Logic\News\NewsLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class NewsCateLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'pid' => $data['pid'],
            'isOn' => $data['isOn'],//是否显示
            'sort' => $data['sort'],
            'tableId'=>$tableId,
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $newsCateId=self::addNewsCate($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'newsCateId' => $newsCateId,
                    'newsCateTitle' => $value['newsCateTitle'],
                    'newsCateImage' => empty($data['newsCateImage']) ? '' : $data['newsCateImage'],
                    'languageId'=>$value['languageId'],
                    'newsCateDesc' => empty($value['newsCateDesc']) ? '' : $value['newsCateDesc'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addNewsCateDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addNewsCate ($data=[])
    {
        $model = new NewsCateModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addNewsCateDesc ($data=[])
    {
        $model = new NewsCateDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取新闻分类列表
    public static function getNewsCateList($data=[],$tableId)
    {
        $model = new NewsCateModel();
        $name1=[];
        $where1=[];
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='news_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1='news_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        $newsLists = $model->whereSiteId()
            ->where('news_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->leftjoin('news_category_description','news_category.news_cate_id', '=','news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.news_cate_id',
                'news_category.pid',
                'news_category.sort',
                'news_category.site_id',
                'news_category.is_on',
                'news_category.table_id',
                'news_category_description.language_id',
                'news_category_description.news_cate_title',
                'news_category_description.news_cate_desc',
                'news_category_description.news_cate_image',
                'news_category_description.site_title',
                'news_category_description.site_keywords',
                'news_category_description.site_description',

            ]);
        $news = TreeLogic::Newstree($newsLists);
        return $news;
    }

    //菜单地址
    public static function getCateLists($data=[],$tableId)
    {
        $model = new NewsCateModel();
        $name1=[];
        $where1=[];
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='news_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1='news_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        $newsLists = $model->whereSiteId()
            ->where('news_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.news_cate_id',
                'news_category.pid',
                'news_category.table_id',
                'news_category_description.news_cate_title',
            ]);
        if(isset($newsLists)){
            foreach ($newsLists as $key=>$value) {
                $newsLists[$key]['propId']='newsCateId';
                $newsLists[$key]['propTitle']='newsCateTitle';
            }
        }
        $news = TreeLogic::Newstree($newsLists);
        return $news;
    }

    //获取新闻分类列表----test
    public static function getCateList($tableId)
    {
        $model = new NewsCateModel();
        $newsLists = $model->whereSiteId()
            ->where('news_category.table_id',$tableId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);
        $news = TreeLogic::Newstree($newsLists);
        return $news;
    }


    //获取主单条
    public static function getNewsCateOne($newsCateId)
    {
        $model = new NewsCateModel();
        $news = $model->where('news_cate_id', $newsCateId)
            ->firstHump(['*']);
        if(isset($news)){
            $newsCateDesc = self::getNewsCateDesc($news['newsCateId']);
            if(isset($newsCateDesc)){
                foreach ($newsCateDesc as $key=>$value){
                    $news['newsCateImage']=empty($value['newsCateImage']) ? '' : $value['newsCateImage'];
                }
                $news['lang']=empty($newsCateDesc) ? [] : $newsCateDesc;
            }
        }
        return $news;
    }

    //获取详情全部
    public static function getNewsCateDesc($newsCateId)
    {
        $model = new NewsCateDescModel();
        $newsCateDesc = $model->where('news_cate_id', $newsCateId)->getHump(['*']);
        return $newsCateDesc;
    }
    //获取详情全部
    public static function getNewsCateName($newsCateId,$languageId)
    {
        $model = new NewsCateDescModel();
        $newsCateDesc = $model->where('news_cate_id', $newsCateId)->where('language_id',$languageId)->firstHump(['news_cate_title']);
        return $newsCateDesc->newsCateTitle;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'newsCateId' => $data['newsCateId'],
            'isOn' => $data['isOn'],
            'sort' => $data['sort']
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $newsCateId=$data['newsCateId'];
            self::editNewsCate($main,$newsCateId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'newsCateTitle' => $value['newsCateTitle'],
                    'newsCateImage' => empty($data['newsCateImage']) ? '' : $data['newsCateImage'],
                    'newsCateDesc' => empty($value['newsCateDesc']) ? '' : $value['newsCateDesc'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editNewsCateDesc($desc,$newsCateId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editNewsCate($data=[],$newsCateId)
    {
        $model = new NewsCateModel();
        $model->where('news_cate_id', $newsCateId)->updateByHump($data);
    }

    //编辑主表
    public static function isShow($data=[],$newsCateId)
    {

        $model = new NewsCateModel();
        $model->where('news_cate_id', $newsCateId)->updateByHump($data);

    }

    //编辑详细表
    public static function editNewsCateDesc($data=[],$newsCateId,$languageId)
    {
        $model = new NewsCateDescModel();
        $model->where('news_cate_id', $newsCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($newsCateId)
    {
        \DB::beginTransaction();
        try{
            self::deleteNewsCate($newsCateId);
            self::deleteNewsCateDesc($newsCateId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteNewsCate($newsCateId)
    {

        $news = NewsLogic::getCateNews($newsCateId);
        if (!empty($news)){
            throw new RJsonError('该类下还有数据', 'DELETE_NEWSCATE');
        }
        $cate = self::getChildId($newsCateId);
        if (!empty($cate)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_CATE_NEWSCATE');
        }
        $model = new NewsCateModel(/*$prefix*/);
        $model->where('news_cate_id', $newsCateId)->delete();
    }
    //删除详
    public static function deleteNewsCateDesc($newsCateId)
    {
        $model = new NewsCateDescModel();
        $model->where('news_cate_id', $newsCateId)->delete();
    }

    //获取下一级
    public static function getChildId($newsCateId)
    {

        $model = new NewsCateModel();
        $news = $model->where('news_category.pid', $newsCateId)
            ->leftjoin('news_category_description','news_category.news_cate_id', '=','news_category_description.news_cate_id')
            ->firstHump([
                'news_category.news_cate_id'
            ]);
        return $news;

    }


    //获取上一级ID
    public static function getNewsCateId($newsCateId)
    {
        $model = new NewsCateModel();
        $news = $model->where('news_category.news_cate_id', $newsCateId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->firstHumpArray([
                'news_category.table_id',
                'news_category.pid'

            ]);
        return $news;

    }

    //=========================前端调用单条==============================

    //查单条
    public static function getNewsCate($newsCateId,$languageId)
    {
        $model = new NewsCateModel();
        $news = $model->where('news_category.news_cate_id', $newsCateId)
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->firstHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);

        return $news;
    }
    //=================================================================

    //获取子类--test
    public static function newCate($newsCateId){
        $model = new NewsCateModel();
        $arr=[];
        $newsLists = $model->whereSiteId()->getHumpArray(['pid','newsCateId']);
        $newsCate = TreeLogic::getSubs($newsLists,$newsCateId);
        if(!empty($newsCate)){
            foreach ($newsCate as $key=>$value){
                $arr[]=$value['newsCateId'];
                if(!empty($value['child'])){
                    foreach ($value['child'] as $val) {
                        $arr[]=$val['newsCateId'];
                        if($value['child']){
                            foreach ($val['child'] as $v) {
                                $arr[]=$v['newsCateId'];
                            }
                        }
                    }
                }
            }
        }
        $newsCateId=(int) $newsCateId;
        array_push($arr,$newsCateId);
        return $arr;
    }


    //获取类下的所有子类ID
    public static function getCateId($newsCateId,$tableId)
    {
        $model = new NewsCateModel();
        $data = $model->whereSiteId()->where('table_id',$tableId)->where('pid',$newsCateId)->getHumpArray(['pid','newsCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['newsCateId'];
            }
        }
        $arr2 = self::Newstree($arr,$tableId);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $newsCateId=(int) $newsCateId;
        array_push($res,$newsCateId);
        return $res;
    }
    public static function Newstree($newsCateId,$tableId) {
        $model = new NewsCateModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereSiteId()->where('table_id',$tableId)->whereIn('pid',$newsCateId)->getHumpArray(['pid','newsCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['newsCateId'];
                $arr2 =self::Newstree($arr,$tableId);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }


    //获取面包宵
    public static function getNewsCateParents($newsCateId,$languageId)
    {
        $model = new NewsCateModel();
        $news = $model->whereSiteId()
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);
        $res = TreeLogic::getNewsParents($news,$newsCateId);
        if(!empty($res)){
            $arr=[];
            foreach ($res as $value) {
                if($value['pid']==0){
                    $res2 = MenuLogic::getMenuNameByClassId($value['newsCateId'],$languageId,2);
                    if(empty($res2)){
                        //如果不存在，去找同级的导航
                        $res2 = self::getMenuNameByBrother($value['tableId'],$languageId);
                        $check=$res2['check'];
                        unset($res2['check']);
                    }else{
                        //检测是否有相同页面的
                        $check=MenuLogic::getMenuCheckByClassId($value['newsCateId'],$languageId,2);
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
                $news = self::getNewsCate($newsCateId,$languageId);
                $urlName='news';
                $menu = MenuLogic::getMenuIdByUrl($urlName,$news['tableId'],$languageId);
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
            $res2 = MenuLogic::getMenuByClassId($value['newsCateId'],$languageId,2);
            $res2['check']=2;
            return $res2;
        }
        return;
    }

    //获取类下所有子类，并统计每个类下新闻的条数
    public static function getNewsCateKids($newsCateId,$tableId,$languageId)
    {
        $model = new NewsCateModel();
        $news = $model->whereSiteId()
            ->where('news_category.table_id',$tableId)
            ->where('news_category.pid',$newsCateId)
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);
        if(!empty($news)){
            foreach ($news as $key=>$value){
                $cateId=self::getCateId($value['newsCateId'],$tableId);
                $count = NewsLogic::newsCount($cateId);
                $news[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['newsCateId'],$tableId);
                if(!empty($res)){
                    $news[$key]['kids']=true;
                }else{
                    $news[$key]['kids']=false;
                }
            }
        }
        return $news;
    }

    //获取父级，并统计每个类下新闻的条数
    public static function getParents($tableId,$languageId){
        $model = new NewsCateModel();
        $news = $model->whereSiteId()
            ->where('news_category.table_id',$tableId)
            ->where('news_category.pid',0)
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);
        if(!empty($news)){
            foreach ($news as $key=>$value){
                $cateId=self::getCateId($value['newsCateId'],$tableId);
                $count = NewsLogic::newsCount($cateId);
                $news[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['newsCateId'],$tableId);
                if(!empty($res)){
                    $news[$key]['kids']=true;
                }else{
                    $news[$key]['kids']=false;
                }

            }
        }
        return $news;
    }

    //menu->banner
    public static function getNewsCateParent($newsCateId,$languageId)
    {
        $model = new NewsCateModel();
        $news = $model->whereSiteId()
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_category_description', 'news_category.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->getHumpArray([
                'news_category.*',
                'news_category_description.*',
            ]);
        $res2 = TreeLogic::getNewsParents($news,$newsCateId);
        if(!empty($res2)){
            foreach ($res2 as $key=>$value){
                $arr = MenuLogic::getMenuBannerByClassId($value['newsCateId'],$languageId,2);
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
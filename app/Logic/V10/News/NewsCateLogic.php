<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\News;

use App\Logic\V10\Common\TreeLogic;
use App\Model\V10\News\NewsCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class NewsCateLogic
{

    //添加主表
    public static function add($data=[])
    {
        if($data['newsCateTitle']=='公司动态'){
            $res = self::getNewsByName($data['newsCateTitle']);
            if(!empty($res)){
                throw new RJsonError('该分类已存在', 'NEWS_CATE_TITLE');
            }
            $data['newsCateId']=1;
        }
        if($data['newsCateTitle']=='娱乐新闻'){
            $res = self::getNewsByName($data['newsCateTitle']);
            if(!empty($res)){
                throw new RJsonError('该分类已存在', 'NEWS_CATE_TITLE');
            }
            $data['newsCateId']=2;
        }
        \DB::beginTransaction();
        try{
            $model = new NewsCateModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return $model;
    }

    public static function Lists($data=[])
    {
        $model = new NewsCateModel();
        if(isset($data['isOn'])){
            $model=$model->where('is_on',1);
        }
        if(isset($data['newsCateTitle'])){
            $model = $model->where('news_cate_title', 'like',$data['newsCateTitle']);
        }
        $lists = $model->getHumpArray();
        $res = TreeLogic::newsTree($lists);
        return $res;
    }

    public static function getNewsByName($name)
    {
        $model = new NewsCateModel();
        $Admin = $model->where('news_cate_title',$name)->firstHumpArray(['*']);
        return $Admin;
    }

    public static function getOne($newsCateId)
    {
        $model = new NewsCateModel();
        $Admin = $model->where('news_cate_id',$newsCateId)->firstHumpArray(['*']);
        return $Admin;
    }

    public static function getCateById($newsCateId)
    {
        $model = new NewsCateModel();
        $Admin = $model->where('pid',$newsCateId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new NewsCateModel();
            $model->where('news_cate_id', $data['newsCateId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($newsCateId)
    {
        \DB::beginTransaction();
        try{
            //查看是否有子类
            $res = self::getCateById($newsCateId);
            if(!empty($res)){
                throw new RJsonError('该分类下还有子类', 'NEWS_CATE_ERROR');
            }
            $new = NewsLogic::getByCateId($newsCateId);
            if(!empty($new)){
                throw new RJsonError('该分类下还有数据', 'NEWS_ERROR');
            }
            $model = new NewsCateModel();
            $model->where('news_cate_id',$newsCateId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //获取类下的所有子类ID
    public static function getCateId($newsCateId)
    {
        $model = new NewsCateModel();
        $data = $model->where('pid',$newsCateId)->getHumpArray(['pid','newsCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['newsCateId'];
            }
        }
        $arr2 = self::NewsTree($arr);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $newsCateId=(int) $newsCateId;
        array_push($res,$newsCateId);
        return $res;
    }
    public static function NewsTree($newsCateId) {
        $model = new NewsCateModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereIn('pid',$newsCateId)->getHumpArray(['pid','newsCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['newsCateId'];
                $arr2 =self::NewsTree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }


}
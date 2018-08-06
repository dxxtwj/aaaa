<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\News;

use App\Model\V10\News\NewsModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class NewsLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['content'])){
            $data['content'] = '';
        }
        \DB::beginTransaction();
        try{
            $model = new NewsModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function Lists($data=[])
    {
        $model = new NewsModel();
        if(isset($data['isOn'])){
            $model=$model->where('news.is_on',1);
        }
        if(isset($data['newsCateId'])){
            $cateId = NewsCateLogic::getCateId($data['newsCateId']);
            $model=$model->whereIn('news.news_cate_id',$cateId);
        }
        if(isset($data['newsTitle'])){
            $model = $model->where('news.news_title', 'like','%' . $data['newsTitle'] . '%');
        }
        $lists = $model->orderBy('news.sort','DESC')
            ->leftJoin('news_category','news.news_cate_id','=','news_category.news_cate_id')
            ->select(['news.*','news_category.news_cate_title']);
        $res = $lists->getDdvPageHumpArray(true);
        return $res;
    }

    public static function getOne($newsId)
    {
        $model = new NewsModel();
        $Admin = $model->where('news_id',$newsId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new NewsModel();
            $model->where('news_id', $data['newsId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($newsId)
    {
        \DB::beginTransaction();
        try{
            $model = new NewsModel();
            $model->where('news_id',$newsId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }
    //推荐
    public static function recommend($number,$cateId)
    {
        $model = new NewsModel();
        $res = $model->where('is_on',1)->where('news_cate_id',$cateId)->where('recommend',1)->limit($number)->getHumpArray(['*']);
        return $res;
    }

    //
    public static function getByCateId($cateId)
    {
        $model = new NewsModel();
        $res = $model->where('news_cate_id',$cateId)->getHumpArray(['*']);
        return $res;
    }


}
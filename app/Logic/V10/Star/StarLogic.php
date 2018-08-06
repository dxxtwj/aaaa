<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Star;

use App\Model\V10\Star\StarModel;
use App\Model\V10\Star\StarRecommendModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class StarLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['content'])){
            $data['content'] = '';
        }
        \DB::beginTransaction();
        try{
            $model = new StarModel();
            $starId = $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        if($data['recommendHome']==1){
            self::recommendHome($starId);
        }
        return;
    }

    public static function Lists($data=[])
    {
        $model = new StarModel();
        if(isset($data['isOn'])){
            $model=$model->where('star.is_on',1);
        }
        if(isset($data['starCateId'])){
            $cateId = StarCateLogic::getCateId($data['starCateId']);
            $model=$model->whereIn('star.star_cate_id',$cateId);
        }
        if(isset($data['starTitle'])){
            $model = $model->where('star.star_title', 'like','%' . $data['starTitle'] . '%');
        }
        $lists = $model->orderBy('star.sort','DESC')
            ->leftJoin('star_category','star.star_cate_id','=','star_category.star_cate_id')
            ->select(['star.*','star_category.star_cate_title']);
        $res = $lists->getDdvPageHumpArray();
        return $res;
    }

    public static function getOne($starId)
    {
        $model = new StarModel();
        $Admin = $model->where('star_id',$starId)->firstHumpArray(['*']);
        return $Admin;
    }

    public static function getOneApi($starId)
    {
        $model = new StarModel();
        $Admin = $model->where('star.star_id',$starId)
            ->leftJoin('star_category','star.star_cate_id','=','star_category.star_cate_id')
            ->firstHumpArray(['star.*','star_category.star_cate_title']);
        return $Admin;
    }

    //推荐
    public static function recommend($number)
    {
        $model = new StarModel();
        $res = $model->where('is_on',1)->where('recommend',1)->limit($number)->getHumpArray(['*']);
        return $res;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new StarModel();
            $model->where('star_id', $data['starId'])->updateByHump($data);
            if($data['recommendHome']==1){
                $res = self::getRecommendHome($data['starId']);
                if(empty($res)){
                    self::recommendHome($data['starId']);
                }
            }else{
                self::recommendHomeDel($data['starId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($starId)
    {
        \DB::beginTransaction();
        try{
            $model = new StarModel();
            $model->where('star_id',$starId)->delete();
            self::deleteRecommendHome($starId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }
    public static function deleteRecommendHome($starId)
    {
        $model = new StarRecommendModel();
        $model->where('star_id',$starId)->delete();
    }

    public static function editRecommend($starId)
    {
        \DB::beginTransaction();
        try{
            $res['recommendHome']=0;
            $model = new StarModel();
            $model->where('star_id',$starId)->updateByHump($res);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //首页推荐
    public static function recommendHome($starId)
    {
        \DB::beginTransaction();
        try{
            $model = new StarModel();
            $data = $model->where('star_id',$starId)->firstHumpArray(['*']);
            $res = [
                'starId'=>$starId,
                'thumb'=>$data['thumb'] ?? '',
                'starTitle'=>$data['starTitle'],
            ];
            $model2 = new StarRecommendModel();
            $model2->setDataByHumpArray($res)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return;
    }


    public static function editRecommendHome($data)
    {
        \DB::beginTransaction();
        try{
            $model = new StarRecommendModel();
            $model->where('star_id',$data['starId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
        return;
    }
    public static function recommendHomeLists($data=[])
    {
        $model = new StarRecommendModel();
        if(isset($data['starTitle'])){
            $model = $model->where('star_title', 'like','%' . $data['starTitle'] . '%');
        }
        $lists = $model->orderBy('sort','ASC')->getDdvPageHumpArray();
        return $lists;
    }
    public static function getRecommendHome($starId)
    {
        $model = new StarRecommendModel();
        $data = $model->where('star_id',$starId)->firstHumpArray();
        return $data;
    }

    public static function recommendHomeDel($starId)
    {
        \DB::beginTransaction();
        try{
            $model = new StarRecommendModel();
            $model->where('star_id',$starId)->delete();
            self::editRecommend($starId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
    }

    //推荐
    public static function recommendHomeApi($number)
    {
        $model = new StarRecommendModel();
        $res = $model->where('is_on',1)->orderBy('sort','ASC')->limit($number)->getHumpArray(['*']);
        return $res;
    }

    //
    public static function getByCateId($cateId)
    {
        $model = new StarModel();
        $res = $model->where('star_cate_id',$cateId)->firstHumpArray(['*']);
        return $res;
    }

}
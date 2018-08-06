<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Cases;

use App\Model\V10\Cases\CasesModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class CasesLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(empty($data['content'])){
            $data['content'] = '';
        }
        \DB::beginTransaction();
        try{
            $model = new CasesModel();
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
        $model = new CasesModel();
        if(isset($data['isOn'])){
            $model=$model->where('cases.is_on',1);
        }
        if(isset($data['casesCateId'])){
            $cateId = CasesCateLogic::getCateId($data['casesCateId']);
            $model=$model->whereIn('cases.cases_cate_id',$cateId);
        }
        if(isset($data['casesTitle'])){
            $model = $model->where('cases.cases_title', 'like','%' . $data['casesTitle'] . '%');
        }
        $lists = $model->orderBy('cases.sort','DESC')
            ->leftJoin('cases_category','cases.cases_cate_id','=','cases_category.cases_cate_id')
            ->select(['cases.*','cases_category.cases_cate_title']);
        $res = $lists->getDdvPageHumpArray(true);
        return $res;
    }

    public static function getOne($casesId)
    {
        $model = new CasesModel();
        $Admin = $model->where('cases_id',$casesId)->firstHumpArray(['*']);
        return $Admin;
    }

    //修改主表
    public static function edit($data)
    {
        \DB::beginTransaction();
        try{
            $model = new CasesModel();
            $model->where('cases_id', $data['casesId'])->updateByHump($data);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    public static function delete($casesId)
    {
        \DB::beginTransaction();
        try{
            $model = new CasesModel();
            $model->where('cases_id',$casesId)->delete();
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
        $model = new CasesModel();
        $res = $model->where('is_on',1)->where('cases_cate_id',$cateId)->where('recommend',1)->limit($number)->getHumpArray(['*']);
        return $res;
    }

    //
    public static function getByCateId($cateId)
    {
        $model = new CasesModel();
        $res = $model->where('cases_cate_id',$cateId)->firstHumpArray(['*']);
        return $res;
    }

    //测试拿content里面的地址
    public static function getContent()
    {
        $model = new CasesModel();
        $Admin = $model->where('cases_id',2)->firstHumpArray(['*']);
        var_dump($Admin['content']);
        //使用正则表达式匹配正文内容中所有的img标签
        $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';//匹配img标签的正则表达式
        preg_match_all($preg, $Admin['content'], $allImg);//这里匹配所有的img
        var_dump($allImg);
        $allImg = array(
            "<img src=\"http://jiahuastar-oss.oss-cn-shenzhen.aliyuncs.com//upload/other/c4b48d07454ce06f5197A9A877.jpg\" style=\"max-width: 100%;\" class=\"\">",
            "<img src=\"http://jiahuastar-oss.oss-cn-shenzhen.aliyuncs.com//upload/other/4123c58186861c9da5543855BE.jpg\" style=\"max-width: 100%;\">",
        );
        if($allImg){
            $Admin['content']=preg_replace("<img src=\"","<img src=\"66666",$Admin['content']);//给所有img标签加上class
        }

        var_dump($Admin['content']);
        return [];
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Old;

use App\Logic\V10\Cases\CasesLogic;
use App\Logic\V10\News\NewsLogic;
use App\Logic\V10\PhotoLogic;
use App\Logic\V10\Star\StarLogic;
use App\Model\V10\Old\CasesOldModel;
use App\Model\V10\Old\NewsOldModel;
use App\Model\V10\Old\PhotoOldModel;
use App\Model\V10\Old\StarOldModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class CasesOldLogic
{

    //案例
    public static function Lists($number,$number2)
    {
        $model = new CasesOldModel();
        $lists = $model->where('id','>',$number)->limit($number2)->getHumpArray();
        //self::addCases($lists);
        return $lists;
    }
    public static function addCases($lists)
    {
        foreach ($lists as $key=>$value)
        {
            $img='';
            if(!empty($value['small'])){
                $res = substr($value['small'],15);
                $img='http://jiahuastar-oss.oss-cn-shenzhen.aliyuncs.com/'.$res;
            }
            $res=[
                'casesCateId'=>$value['cid'],
                'casesTitle'=>$value['title'],
                'thumb'=>$img,
                'content'=>$value['desc'],
                'seoTitle'=>$value['title'],
                'seoKeywords'=>$value['title'],
                'seoDescription'=>$value['title'],
                'createdAt'=>$value['addTime'],
                'updatedAt'=>$value['upTime'],
            ];
            CasesLogic::add($res);
        }
    }

    //新闻
    public static function getNews($number,$number2)
    {
        $model = new NewsOldModel();
        $lists = $model->where('id','>',$number)->limit($number2)->getHumpArray();
        //self::addNews($lists);
        return $lists;
    }
    public static function addNews($lists)
    {
        foreach ($lists as $key=>$value)
        {
            $res=[
                'newsCateId'=>$value['cid'],
                'newsTitle'=>$value['title'],
                'content'=>$value['desc'],
                'seoTitle'=>$value['title'],
                'seoKeywords'=>$value['title'],
                'seoDescription'=>$value['title'],
                'createdAt'=>$value['addTime'],
                'updatedAt'=>$value['upTime'],
            ];
            NewsLogic::add($res);
        }
    }

    //合影
    public static function getPhoto($number,$number2)
    {
        $model=new PhotoOldModel();
        $lists = $model->where('id','>',$number)->limit($number2)->getHumpArray();
        //self::addPhoto($lists);
        return $lists;
    }
    public static function addPhoto($lists)
    {
        foreach ($lists as $key=>$value)
        {
            $img='';
            if(!empty($value['small'])){
                $res = substr($value['small'],15);
                $img='http://jiahuastar-oss.oss-cn-shenzhen.aliyuncs.com/'.$res;
            }
            $res=[
                'photoTitle'=>$value['title'],
                'thumb'=>$img,
                'content'=>$value['desc'],
                'seoTitle'=>$value['title'],
                'seoKeywords'=>$value['title'],
                'seoDescription'=>$value['title'],
                'createdAt'=>$value['addTime'],
                'updatedAt'=>$value['upTime'],
            ];
            PhotoLogic::add($res);
        }
    }

    public static function getOne($bannerId)
    {
        $model = new CasesOldModel();
        $Admin = $model->where('banner_id',$bannerId)->firstHumpArray(['*']);
        return $Admin;
    }

    //合影
    public static function getStar($number,$number2)
    {
        $model=new StarOldModel();
        $lists = $model->where('id','>',$number)->limit($number2)->getHumpArray();
        //self::addStar($lists);
        return $lists;
    }
    public static function addStar($lists)
    {
        foreach ($lists as $key=>$value)
        {
            $img='';
            if(!empty($value['small'])){
                $res = substr($value['small'],15);
                $img='http://jiahuastar-oss.oss-cn-shenzhen.aliyuncs.com/'.$res;
            }
            $res=[
                'starCateId'=>$value['cid'],
                'starTitle'=>$value['title'],
                'number'=>$value['Itemno'],
                'type'=>$value['type'],
                'recommendHome'=>0,
                'broker'=>'李志',
                'phone'=>'13428888909 15601590173',
                'thumb'=>$img,
                'content'=>$value['desc'],
                'endorsementFee'=>$value['dyjg'],
                'appearanceFee'=>$value['ccjg'],
                'seoTitle'=>$value['title'],
                'seoKeywords'=>$value['title'],
                'seoDescription'=>$value['title'],
                'createdAt'=>$value['addTime'],
            ];
            StarLogic::add($res);
        }
    }


}
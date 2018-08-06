<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\School;

use App\Model\School\ArtclassModel;
use App\Model\School\ArtModel;
use App\Model\News\NewsModel;
use App\Model\News\NewsDescModel;
use App\Model\SiteModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class SchoolTransferLogic
{

    //获取文章分类
    public static function getArtclass()
    {
        $Artclass = ArtclassModel::select(['*']);
        $res = $Artclass->getDdvPageHumpArray(true);
        return $res;
    }

    //获取文章
    public static function getArt($class)
    {
        $Artclass = ArtModel::where('class',$class)->getHumpArray(['*']);
        $arr=[];
        if($Artclass){
            foreach ($Artclass as $key=>$value){
                $key=$key+1;
                //时间转换
                $time=strtotime($value['date']);
                //主
                $arr=[
                    'siteId'=>16,
                    'tableId'=>8,
                    'newsHit'=>empty($value['click']) ? 0 : $value['click'],
                    'newsCateId'=>102,
                    'sort'=>$key,
                    'created_at'=>$time,
                    'updated_at'=>$time
                ];
                //$newsId=self::addNewsMian($arr);
                $arr2=[
                    'newsId'=>$newsId,
                    'languageId'=>1,
                    'newsTitle'=>empty($value['title']) ? '' : $value['title'],
                    'newsAuthor'=>empty($value['user']) ? '' : $value['user'],
                    'newsContent'=>empty($value['Content']) ? '' : $value['Content'],
                    'newsThumb'=>empty($value['pic']) ? '' : $value['pic'],
                ];
                //self::addNewsDescs($arr2);
            }
        }
        return $arr;
    }

    //添加新闻主表
    public static function addNewsMian($data)
    {
        $model = new NewsModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //添加详细表
    public static function addNewsDescs ($data=[])
    {
        $model = new NewsDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }


}
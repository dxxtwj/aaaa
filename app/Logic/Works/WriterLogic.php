<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Works;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Works\WriterModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class WriterLogic
{
    //添加主表
    public static function addWriter ($data=[])
    {
        //文件
        $GalleryId=0;
        if(!empty($data['headimg'])){
            $GalleryId = GalleryLogic::getGalleryId($data['headimg']);
        }
        $data['galleryId']=$GalleryId;
        if(empty($data['description'])){
            $data['description']='';
        }
        $model = new WriterModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return;
    }

    //获取列表
    public static function getWriterList($data)
    {

        if (isset($data['name'])) {
            $name = '%' . $data['name'] . '%';
        } else {
            $name = '%';
        }
        $writer = WriterModel::whereSiteId()
            ->where('name', 'like', $name)
            ->orderby('sort','DESC')
            ->select(['*']);
        return $writer->getDdvPageHumpArray(true);
    }

    //查单条
    public static function getWriter($writerId)
    {
        $banner = WriterModel::where('writer_id', $writerId)
            ->firstHump(['*']);
        return $banner;
    }


    //编辑主表
    public static function editWriter($data)
    {
        if(empty($data['description'])){
            $data['description']='';
        }
        //文件
        $GalleryId=0;
        if(!empty($data['headimg'])){
            $GalleryId = GalleryLogic::getGalleryId($data['headimg']);
        }
        if(empty($data['headimg'])){
            $data['headimg']='';
        }
        $data['galleryId']=$GalleryId;
        WriterModel::where('writer_id', $data['writerId'])->updateByHump($data);
    }

    //是否显现
    public static function isShow($data=[],$writerId)
    {
        WriterModel::where('writer_id', $writerId)->updateByHump($data);
    }

    //删除主
    public static function deleteWriter($writerId)
    {
        //先看看下面有没有作品
        $works = WorksLogic::getWorksByWriter($writerId);
        if($works){
            throw new RJsonError("该作者下还有数据", 'WRITER_WORKS');
        }
        (new WriterModel())->where('writer_id', $writerId)->delete();
    }




}
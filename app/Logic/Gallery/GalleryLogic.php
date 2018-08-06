<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Gallery;

use App\Logic\AboutLogic;
use App\Logic\BannerLogic;
use App\Model\Gallery\FileModel;
use App\Model\Gallery\GalleryModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use OSS\OssClient;
use function var_dump;

class GalleryLogic
{
    //添加
    public static function addGallery($data)
    {
        $model = new GalleryModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //获取列表
    public static function getGalleryLists()
    {
        //刷新数据
        self::Refresh();
        $count=0;
        $gallery = GalleryModel::whereSiteId()->select(['*']);
        $res = $gallery->getDdvPageHumpArray(true);
        if(!empty($res['lists'])){
            //计算总大小
            $count = self::AllSize();
        }
        $res['data']['allSize']=$count;
        return $res;
    }
    //刷新数据
    public static function Refresh()
    {
        $gallery = GalleryModel::whereSiteId()->getHumpArray(['*']);
        foreach ($gallery as $value){
            if(empty($value['name'])){
                self::getFile($value['galleryId'],$value['galleryUrl']);
            }
        }
        return;
    }
    //总大小
    public static function AllSize()
    {
        $count=0;
        $gallery = GalleryModel::whereSiteId()->getHumpArray(['gallery_size']);
        foreach ($gallery as $value){
            $count +=$value['gallerySize'];
        }
        return $count;
    }
    //获取单条
    public static function getGallery($galleryUrl)
    {
        $gallery = GalleryModel::whereSiteId()->where('gallery_url',$galleryUrl)->firstHumpArray(['*']);
        return $gallery;
    }
    //获取单条
    public static function getGalleryById($galleryId)
    {
        $gallery = GalleryModel::whereSiteId()->where('gallery_id',$galleryId)->firstHumpArray(['*']);
        return $gallery;
    }

    //获取galleryId
    public static function getGalleryId($galleryUrl)
    {
        $GalleryId=0;
        $Gallery = self::getGallery($galleryUrl);
        if(!empty($Gallery)){
            $GalleryId = $Gallery['galleryId'];
        }
        if(empty($Gallery)){
            $Gallery=[
                'galleryUrl'=>$galleryUrl
            ];
            $GalleryId = self::addGallery($Gallery);
        }
        return $GalleryId;
    }

    //查文件信息
    public static function getFile($galleryId,$url)
    {
        $arr = parse_url($url);
        $path = substr($arr['path'],1);
        $file = FileModel::where('path',$path)->firstHumpArray(['*']);
        if(empty($file)){
            self::checkGallery($galleryId);
        }
        if(!empty($file)){
            self::editGallery($galleryId,$file);
        }
    }

    //查是否名称、基本信息是否为空
    public static function checkGallery($galleryId)
    {
        $gallery = GalleryModel::whereSiteId()->where('gallery_id',$galleryId)->firstHumpArray(['*']);
        if(!empty($gallery)){
            if(empty($gallery['name']) && empty($gallery['type'])){
                self::deleteImage($galleryId);
            }
        }else{
            self::deleteImage($galleryId);
        }
    }

    //修改文件
    public static function editGallery($galleryId,$file)
    {
        $gallery = [
            'name'=>empty($file['name']) ? '' : $file['name'],
            'type'=>empty($file['type']) ? '' : $file['type'],
            'extName'=>empty($file['extName']) ? '' : $file['extName'],
            'gallerySize'=>empty($file['size']) ? '' : $file['size'],
        ];
        $model = new GalleryModel();
        $model->where('gallery_id', $galleryId)->updateByHump($gallery);
        return;
    }

    //删除用
    public static function deleteArray($gallery)
    {
        if(!empty($gallery)){
            foreach ($gallery as $value){
                self::delete($value['galleryId']);
            }
        }
    }

    public static function delete($galleryId)
    {
        \DB::beginTransaction();
        try{
            //获取路径
            $gallery = self::getGalleryById($galleryId);
            //删除阿里云图片
            //$path = self::path($gallery['galleryUrl']);
            //self::deleteOssImage($path);
            //删除数据库的上传文件
            //self::deleteFile($gallery['galleryUrl']);
            //删除文件库
            self::deleteImage($galleryId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
        }
    }

    //删除
    public static function deleteImage($galleryId)
    {
        (new GalleryModel())->where('gallery_id',$galleryId)->delete();
    }

    //截取路径
    public static function path($url)
    {
        $arr = parse_url($url);
        $path = substr($arr['path'],2);
        return $path;
    }

    //删除总文件文件
    public static function deleteFile($url)
    {
        $arr = parse_url($url);
        $path = substr($arr['path'],1);
        (new FileModel())->where('path',$path)->delete();
    }

    //删除阿里云图片
    public static function deleteOssImage($objects)
    {
        //$objects='test2/';
        if(!empty($objects)){
            if($objects=='test/'){
                throw new RJsonError('删除失败', 'FILE_DELETE');
            }
            if($objects=='test2/'){
                throw new RJsonError('删除失败', 'FILE_DELETE');
            }
            if($objects=='upload/'){
                throw new RJsonError('删除失败', 'FILE_DELETE');
            }
            if($objects=='upload/other/'){
                throw new RJsonError('删除失败', 'FILE_DELETE');
            }
        }else{
            throw new RJsonError('删除失败', 'FILE_DELETE');
        }
        //$objects='test/14805643046757.jpg';
        $accessKeyId='LTAIgpKchru0UhWp';
        $accessKeySecret='4BeaRPBTf9q72RJrdd7YFwDjmOMhU3';
        $endpoint='oss-cn-shenzhen.aliyuncs.com';
        $bucket='automakesize-oss';
        //$objects='upload/other/14805841553018.jpg';
        $OssClient= new OssClient($accessKeyId,$accessKeySecret,$endpoint);
        $OssClient->deleteObject($bucket,$objects);
        return;
    }


}
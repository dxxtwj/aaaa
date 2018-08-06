<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Logic\Gallery\GalleryLogic;
use App\Model\Link\LinkModel;
use App\Model\Link\LinkDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class LinkLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn']
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['linkImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['linkImage']);
            }
            $linkId = self::addLink($main);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'linkId' => $linkId,
                    'galleryId'=>$GalleryId,
                    'linkTitle' => empty($value['linkTitle']) ? '' : $value['linkTitle'],
                    'linkUrl' => empty($data['linkUrl']) ? '' : $data['linkUrl'],
                    'linkImage' => empty($data['linkImage']) ? '' : $data['linkImage'],
                    'languageId'=>$value['languageId'],
                ];
                self::addLinkDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addLink ($data=[])
    {
        $model = new LinkModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addLinkDesc ($data=[])
    {
        $model = new LinkDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getLinkList($data)
    {
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='link_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='link.is_on';
            $show=$data['isOn'];
        }
        $linkLists = LinkModel::whereSiteId()
            ->where($name,$where)
            ->where($showName,$show)
            ->orderby('link.sort','DESC')
            ->leftjoin('link_description', 'link.link_id', '=', 'link_description.link_id')
            ->getHumpArray([
                'link.*',
                'link_description.*',
            ]);
        return $linkLists;
    }

    //获取住单条
    public static function getLinkOne($linkId)
    {
        $link = LinkModel::where('link_id', $linkId)->firstHump(['*']);
        if(isset($link)){
            $LinkDesc=self::getLinkDesc($link['linkId']);
            if(isset($LinkDesc)){
                foreach ($LinkDesc as $key=>$value){
                    $link['linkUrl']=empty($value['linkUrl']) ? '' : $value['linkUrl'];
                    $link['linkImage']=empty($value['linkImage']) ? '' : $value['linkImage'];
                }
            }
        }
        $link['lang']=empty($LinkDesc) ? '' : $LinkDesc;
        return $link;
    }
    //获取详情全部
    public static function getLinkDesc($linkId)
    {
        $LinkDeac = LinkDescModel::where('link_id', $linkId)
            ->getHump(['*']);
        return $LinkDeac;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'sort' => $data['sort'],
            'isOn' => $data['isOn'],
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['linkImage'])){
                $GalleryId = GalleryLogic::getGalleryId($data['linkImage']);
            }
            $linkId=$data['linkId'];
            self::editLink($main,$linkId);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'linkTitle' => empty($value['linkTitle']) ? '' : $value['linkTitle'],
                    'linkUrl' => empty($data['linkUrl']) ? '' : $data['linkUrl'],
                    'linkImage' => empty($data['linkImage']) ? '' : $data['linkImage'],
                ];
                self::editLinkDesc($desc,$linkId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editLink($data=[],$linkId)
    {
        LinkModel::where('link_id', $linkId)->updateByHump($data);
    }

    //编辑主表
    public static function isShow($data=[],$linkId)
    {
        LinkModel::where('link_id', $linkId)->updateByHump($data);
    }

    //编辑详细表
    public static function editLinkDesc($data=[],$linkId,$languageId)
    {
        LinkDescModel::where('link_id', $linkId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($linkId)
    {
        \DB::beginTransaction();
        try{
            self::deleteLink($linkId);
            self::deleteLinkDesc($linkId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteLink($linkId)
    {
        (new LinkModel())->where('link_id', $linkId)->delete();
    }
    //删除详
    public static function deleteLinkDesc($linkId)
    {
        (new LinkDescModel())->where('link_id', $linkId)->delete();
    }

    //=========================前端调用单条==============================
    //查单条
    public static function getLink($linkId,$languageId)
    {
        $banner = LinkModel::where('link.link_id', $linkId)
            ->where('link_description.language_id',$languageId)
            ->leftjoin('link_description', 'link.link_id', '=', 'link_description.link_id')
            ->firstHumpArray([
                'link.*',
                'link_description.*',
            ]);

        return $banner;
    }


}
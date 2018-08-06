<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\About;

use App\Logic\Common\TreeLogic;
use App\Model\About\AboutCateModel;
use App\Model\About\AboutModel;
use App\Model\About\AboutCateDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class AboutCateLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $mian=[
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort']
        ];
        $aboutCateId=self::addAboutCate($mian);
        foreach ($data['lang'] as $key=>$value ){
            $desc=[
                'aboutCateId' => $aboutCateId,
                'aboutCateTitle' => $value['aboutCateTitle'],
                'aboutCateThumb' => empty($data['aboutCateThumb']) ? '' : $data['aboutCateThumb'],
                'languageId'=>$value['languageId'],
                'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
            ];
            self::addAboutCateDesc($desc);
        }
    }


    //添加主表
    public static function addAboutCate ($data=[])
    {
        $prefix='about2_category';
        $model = new AboutCateModel($prefix);
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addAboutCateDesc ($data=[])
    {
        $prefix='about2_category_description';
        $model = new AboutCateDescModel($prefix);
        $model->setDataByHumpArray($data)->save();
        return $model;
    }


    //获取新闻分类列表
    public static function getAboutCateList($data=[])
    {
        $prefix='about2_category';
        $prefix2='about2_category_description';
        $model = new AboutCateModel($prefix);
        $name=[];
        $where=[];
        $name1=[];
        $where1=[];
        if(isset($data['languageId'])){
            $name=$prefix2.'.language_id';
            $where=$data['languageId'];
        }

//        else{
//            $name1 =$prefix2.'.language_id';
//            $where1 ='1';
//        }
        $AboutLists = $model->whereSiteId()
            ->where($name,$where)
            ->where($name1,$where1)
            ->leftjoin($prefix2, $prefix.'.about_cate_id', '=', $prefix2.'.about_cate_id')
            ->getHumpArray([
                $prefix.'.about_cate_id',
                $prefix.'.pid',
                $prefix.'.sort',
                $prefix.'.site_id',
                $prefix.'.is_on',
                $prefix2.'.about_cate_id',
                $prefix2.'.language_id',
                $prefix2.'.about_cate_title',
                $prefix2.'.about_cate_thumb',
                $prefix2.'.site_title',
                $prefix2.'.site_keywords',
                $prefix2.'.site_description',

            ]);
        $About = TreeLogic::Abouttree($AboutLists);
        return $About;
    }

    //获取主单条
    public static function getAboutCateOne($aboutCateId)
    {
        $prefix='about2_category';
        $model = new AboutCateModel($prefix);
        $About = $model->where('about_cate_id', $aboutCateId)
            ->firstHump(['*']);
        if(isset($About)){
            $AboutCateDesc = self::getAboutCateDesc($About['aboutCateId']);
            if(isset($AboutCateDesc)){
                foreach ($AboutCateDesc as $key=>$value){
                    $About['aboutCateThumb']=empty($value['aboutCateThumb']) ? '' : $value['aboutCateThumb'];
                }
            }
        }
        $About['lang']=empty($AboutCateDesc) ? '[]' : $AboutCateDesc;
        return $About;
    }

    //获取详情全部
    public static function getAboutCateDesc($aboutCateId)
    {
        $prefix='about2_category_description';
        $model = new AboutCateDescModel($prefix);
        $AboutCateDesc = $model->where('about_cate_id', $aboutCateId)->getHump(['*']);
        return $AboutCateDesc;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $aboutCateId=$data['aboutCateId'];
        $mian=[
            'isOn' => $data['isOn'],
            'sort'=>$data['sort']
        ];
        self::editAboutCate($mian,$aboutCateId);
        foreach ($data['lang'] as $key=>$value){
            $desc=[
                'aboutCateTitle' => $value['aboutCateTitle'],
                'aboutCateThumb' => empty($data['aboutCateThumb']) ? '' : $data['aboutCateThumb'],
                'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
            ];
            self::editAboutCateDesc($desc,$aboutCateId,$value['languageId']);
        }
    }

    //编辑主表
    public static function editAboutCate($data=[],$aboutCateId)
    {
        AboutCateModel::where('about_cate_id', $aboutCateId)->updateByHump($data);

    }
    //编辑详细表
    public static function editAboutCateDesc($data=[],$aboutCateId,$languageId)
    {
        $prefix='about2_category_description';
        $model = new AboutCateDescModel($prefix);
        AboutCateDescModel::where('about_cate_id', $aboutCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除主
    public static function deleteAboutCate($aboutCateId)
    {
        $About = self::getAbout($aboutCateId);
        if (!empty($About)){
            throw new RJsonError('该类下还有数据', 'DELETE_ABOUTCATE');
        }
        $cate = self::getChildId($aboutCateId);
        if (isset($cate)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_ABOUT_CATE');
        }
        (new AboutCateModel())->where('about_cate_id', $aboutCateId)->delete();
        self::deleteAboutCateDesc($aboutCateId);
    }
    //查主表单条
    public static function getAbout($aboutCateId)
    {
        $About = AboutModel::where('about_cate_id', $aboutCateId)
            ->firstHump(['*']);
        return $About;
    }
    //删除详
    public static function deleteAboutCateDesc($aboutCateId)
    {
        (new AboutCateDescModel())->where('about_cate_id', $aboutCateId)->delete();
    }

    //获取上一级ID
    public static function getAboutCateId($aboutCateId)
    {
        $about = AboutCateModel::where('about_category.about_cate_id', $aboutCateId)
            ->leftjoin('about_category_description', 'about_category.about_cate_id', '=', 'about_category_description.about_cate_id')
            ->firstHump([
                'about_category.pid'
            ]);
        return $about;

    }

    //获取下一级
    public static function getChildId($aboutCateId)
    {
        $About = AboutCateModel::where('about_category.pid', $aboutCateId)
            ->leftjoin('about_category_description', 'about_category.about_cate_id', '=', 'about_category_description.about_cate_id')
            ->firstHump([
                'about_category.about_cate_id'
            ]);
        return $About;

    }

    //=========================前端调用单条=============================

    //查单条
    public static function getAboutCate($aboutCateId,$languageId)
    {
        $aboutCate = AboutCateModel::where('about_category.about_cate_id', $aboutCateId)
            ->where('about_category_description.language_id',$languageId)
            ->leftjoin('about_category_description', 'about_category.about_cate_id', '=', 'about_category_description.about_cate_id')
            ->getHumpArray([
                'about_category.*',
                'about_category_description.*',
            ]);

        return $aboutCate;
    }



}
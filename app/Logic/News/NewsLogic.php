<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\News;

use App\Logic\Common\NameSortLogic;
use App\Logic\Gallery\GalleryLogic;
use App\Model\News\NewsModel;
use App\Model\News\NewsDescModel;
use App\Model\News\NewsimageModel;
use App\Model\News\NewsBannerModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class NewsLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'newsCateId' => $data['newsCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'newsUrl' => empty($data['newsUrl']) ? '' : $data['newsUrl'],
            'isOn' => $data['isOn'],
            'tableId'=>$tableId

        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['newsThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['newsThumb']);
            }
            $newsId=self::addNews($main,$data['tableId']);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'newsId' => $newsId,
                    'galleryId'=>$GalleryId,
                    'newsTitle' => $value['newsTitle'],
                    'newsAuthor' => $value['newsAuthor'],
                    'newsContent' => empty($value['newsContent']) ? '' : $value['newsContent'],
                    'newsDesc' => empty($value['newsDesc']) ? '' : $value['newsDesc'],
                    'newsThumb' => empty($data['newsThumb']) ? '' : $data['newsThumb'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addNewsDesc($desc);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //广告图
            if(isset($data['newsbanner'])){
                self::addNewsBanner($data['newsbanner'],$newsId);
            }
            //添加图片
            if(isset($data['lang'])){
                foreach ($data['lang'] as $key=>$value){
                    //图片
                    if(!empty($value['photos'])){
                        self::addNewsimage($value['photos'],$newsId);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addNews ($data=[],$tableId)
    {
        $model = new NewsModel();
        //检查排序
        self::Sort($data['newsCateId'],$data['sort'],$tableId);
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //查排序是否唯一
    public static function Sort ($newsCateId,$sort,$tableId)
    {
        $model = new NewsModel();
        $res = $model->whereSiteId()->where('table_id',$tableId)->where('news_cate_id',$newsCateId)->where('sort',$sort)->firstHump(['*']);
        $sort = $model->whereSiteId()->where('table_id',$tableId)->where('news_cate_id',$newsCateId)->orderby('sort','DESC')->firstHump(['sort']);
        if(!empty($res)){
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'NEWS_SORT');
        }
    }

    //添加详细表
    public static function addNewsDesc ($data=[])
    {
        $model = new NewsDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //添加图片表
    public static function addNewsimage ($data=[],$newsId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['newsImagePic']);
            $newsimage = [
                'newsId' => $newsId,
                'galleryId' => $GalleryId,
                'newsImagePic' => $value['newsImagePic'],
                'newsImageDesc' => empty($value['newsImageDesc']) ? '' : $value['newsImageDesc'],
                'languageId'=>$value['languageId'],
            ];
            $model = new NewsimageModel();
            $model->setDataByHumpArray($newsimage)->save();
        }
    }

    //添加广告
    public static function addNewsBanner ($data=[],$newsId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['newsBannerPic']);
            $newsBanner = [
                'newsId' => $newsId,
                'galleryId' => $GalleryId,
                'newsBannerPic' => $value['newsBannerPic'],
                'sort'=>empty($value['sort']) ? 1 : $value['sort'],
            ];
            $model = new NewsBannerModel();
            $model->setDataByHumpArray($newsBanner)->save();
        }
    }


    //获取新闻列表
    public static function getNewsListTest($data,$tableId)
    {
        $model = new NewsModel();
        $where=[];
        $name=[];
        if(isset($data['languageId'])){
            $name='news_description.language_id';
            $where=$data['languageId'];
        }
        if (isset($data['newsTitle'])) {
            $newsTitle = '%' . $data['newsTitle'] . '%';
        } else {
            $newsTitle = '%';
        }
        //排序
        $sort='sort';
        if(isset($data['newsSort'])){
            $sort='news_hit';
        }
        if(isset($data['newsCateId'])){
            $name3='news.news_cate_id';
            $cateId=NewsCateLogic::getCateId($data['newsCateId'],$tableId);
            $newsLists = $model->whereSiteId()
                ->where('news.table_id',$tableId)
                ->where($name,$where)
                ->whereIn($name3,$cateId)
                ->orderby('news.'.$sort,'DESC')
                ->where('news_description.news_title', 'like', $newsTitle)
                ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
                ->select([
                    'news.*',
                    'news_description.*',
                ]);
        }else{
            $newsLists = $model->whereSiteId()
                ->where('news.table_id',$tableId)
                ->where($name,$where)
                ->orderby('news.'.$sort,'DESC')
                ->where('news_description.news_title', 'like', $newsTitle)
                ->leftjoin('news_description','news.news_id', '=', 'news_description.news_id')
                ->select([
                    'news.*',
                    'news_description.*',
                ]);
        }
        $res=$newsLists->getDdvPageHumpArray(true);
        if(isset($res)){
            $settlesRes = array();
            foreach ($res['lists'] as $key=>$value){
                $name=NewsCateLogic::getNewsCateName($value['newsCateId'],$value['languageId']);
                $res['lists'][$key]['newsCateTitle']=empty($name) ? '' : $name;
                if(isset($data['nameSort'])){
                    $nameFirstChar = NameSortLogic::getFirstCharter($value['newsTitle']);
                    $settlesRes[] = $nameFirstChar;//以这个首字母作为key
                }
            }
            if(isset($data['nameSort'])){
                array_multisort($settlesRes, SORT_ASC, $res['lists']);
            }
        }
        return $res;
    }

    //获取住单条
    public static function getNewsOneTest($newsId)
    {
        $model = new NewsModel();
        $news = $model->whereSiteId()
            ->where('news_id', $newsId)
            ->firstHump(['*']);
        if(isset($news)){
            $NewsDesc=self::getNewsDesc($news['newsId']);
            if(!empty($NewsDesc)){
                foreach ($NewsDesc as $key=>$value){
                    $news['newsThumb']=empty($value['newsThumb']) ? '' : $value['newsThumb'];
                    $image = self::getImage($news['newsId'],$value['languageId']);
                    $value['photos']=empty($image) ? [] : $image;
                }
                $banner = self::getBanner($newsId);
                $news['newsbanner']=empty($banner) ? [] : $banner;
                $news['lang']=empty($NewsDesc) ? [] : $NewsDesc;
            }
        }
        return $news;
    }
    //获取详情全部
    public static function getNewsDesc($newsId)
    {
        $model = new NewsDescModel();
        $NewsDesc = $model->where('news_id', $newsId)->getHump(['*']);
        return $NewsDesc;
    }

    public static function getCateNews($newsCateId)
    {

        $model = new NewsModel();
        $news = $model->where('news_cate_id', $newsCateId)
            ->firstHump(['*']);
        return $news;
    }


    //获取图片
    public static function getImage($newsId,$languageId)
    {
        $model = new NewsimageModel();
        $image=$model->where('news_id',$newsId)
            ->where('language_id',$languageId)
            ->getHumpArray(['news_image_pic','news_image_desc','language_id']);

//        if (empty($image)) {
//
//            $image[0]['newsImagePic']  = '';
//            $image[1]['newsImageDesc'] = '';
//        }
        return $image;
    }

    //获取广告
    public static function getBanner($newsId)
    {
        $model = new NewsBannerModel();
        $image=$model->where('news_id',$newsId)
            ->orderBy('sort','DESC')
            ->getHump(['news_banner_pic','sort']);
        foreach ($image as $k => $v) {
            $image[$k]['newsBannerPic'] ? $image[$k]['newsBannerPic'] : '';
        }

        return $image;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'newsCateId' => $data['newsCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'newsUrl' => empty($data['newsUrl']) ? '' : $data['newsUrl'],
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
            if(!empty($data['newsThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['newsThumb']);
            }
            $newsId=$data['newsId'];
            self::editNews($main,$newsId,$data['tableId']);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId' => $GalleryId,
                    'newsTitle' => $value['newsTitle'],
                    'newsAuthor' => $value['newsAuthor'],
                    'newsContent' => empty($value['newsContent']) ? '' : $value['newsContent'],
                    'newsDesc' => empty($value['newsDesc']) ? '' : $value['newsDesc'],
                    'newsThumb' => empty($data['newsThumb']) ? '' : $data['newsThumb'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editNewsDesc($desc,$newsId,$value['languageId']);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //删除图片
            self::editNewsimage($newsId);
            //删除广告
            self::editNewsBanner($newsId);
            //广告
            if(isset($data['newsbanner'])){
                self::addNewsBanner($data['newsbanner'],$newsId);
            }
            //添加图片
            if(isset($data['lang'])){
                //图片
                foreach ($data['lang'] as $key=>$value){
                    //图片
                    if(isset($value['photos'])){
                        self::addNewsimage($value['photos'],$newsId);
                    }
                }
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }


    //编辑主表
    public static function editNews($data=[],$newsId,$tableId)
    {
        $model = new NewsModel();
        //检查排序
        self::SortTwo($data['newsCateId'],$data['sort'],$newsId,$tableId);
        $model->where('news_id', $newsId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$newsId)
    {
        $model = new NewsModel();
        $model->where('news_id', $newsId)->updateByHump($data);

    }

    //查排序是否唯一
    public static function SortTwo ($newsCateId,$sort,$newsId,$tableId)
    {
        $model = new NewsModel();
        $res = $model->whereSiteId()->where('table_id',$tableId)->where('news_cate_id',$newsCateId)->where('news_id','<>',$newsId)->where('sort',$sort)->firstHump(['*']);
        if(!empty($res)){
            $sort = $model->whereSiteId()->where('table_id',$tableId)->where('news_cate_id',$newsCateId)->orderby('sort','DESC')->firstHump(['sort']);
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'NEWS_SORT');
        }
    }
    //编辑详细表
    public static function editNewsDesc($data=[],$newsId,$languageId)
    {
        $model = new NewsDescModel();
        $model->where('news_id', $newsId)->where('language_id', $languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($newsId)
    {
        \DB::beginTransaction();
        try{
            self::deleteNews($newsId);
            self::deleteNewsDesc($newsId);
            self::editNewsimage($newsId);
            self::editNewsBanner($newsId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteNews($newsId)
    {
        $model = new NewsModel();
        $model->where('news_id', $newsId)->delete();
    }
    //删除详
    public static function deleteNewsDesc($newsId)
    {
        $model = new NewsDescModel();
        $model->where('news_id', $newsId)->delete();
    }

    //图片删、该
    public static function editNewsimage($newsId)
    {
        //查是否有图片
        $image = self::getAllImage($newsId);
        if(isset($image)){
            //有图片把它删除
            self::deleteNewsImage($newsId);
        }
    }

    //广告图片删、该
    public static function editNewsBanner($newsId)
    {
        //查是否有图片
        $image = self::getAllBanner($newsId);
        if(isset($image)){
            //有图片把它删除
            self::deleteNewsBanner($newsId);
        }
    }

    //获取图片
    public static function getAllImage($newsId)
    {
        $model = new NewsimageModel();
        $image=$model->where('news_id',$newsId)
            ->getHump(['news_image_pic','news_image_desc','language_id']);
        return $image;
    }

    //获取广告图
    public static function getAllBanner($newsId)
    {
        $model = new NewsBannerModel();
        $image=$model->where('news_id',$newsId)
            ->orderBy('sort','DESC')
            ->getHump(['news_banner_pic','sort']);
        return $image;
    }

    //删除图片
    public static function deleteNewsImage($newsId)
    {

        $model = new NewsimageModel();
        $model->where('news_id', $newsId)->delete();
    }

    //删除广告
    public static function deleteNewsBanner($newsId)
    {
        $model = new NewsBannerModel();
        $model->where('news_id', $newsId)->delete();
    }

    //=========================前端调用单条=============================

    //查单条
    public static function getNews($newsId,$languageId)
    {

        $model = new NewsModel();
        $news= $model->where('news.news_id', $newsId)
            ->where('news_description.language_id',$languageId)
            ->where('news_category_description.language_id',$languageId)
            ->leftjoin('news_description','news.news_id', '=', 'news_description.news_id')
            ->leftjoin('news_category_description', 'news.news_cate_id', '=', 'news_category_description.news_cate_id')
            ->firstHump([
                'news.*',
                'news_description.*',
                'news_category_description.news_cate_title',
            ]);
        if(isset($news)){
            //图片
            $image = self::getImage($news['newsId'],$languageId);
            $news['photos']=empty($image) ? [] : $image;
            //广告
            $banner = self::getBanner($news['newsId']);
            $news['newsbanner']=empty($banner) ? [] : $banner;
            //点击量
            $hit['newsHit']=$news['newsHit']+1;
            self::Click($hit,$news['newsId']);
        }
        return $news;
    }

    //点击量
    public static function Click($data=[],$newsId)
    {
        $model = new NewsModel();
        $model->where('news_id', $newsId)->updateByHump($data);
    }

    //获取上一条数据
    public static function getLast($newsCateId,$languageId,$sort)
    {
        $model = new NewsModel();
        $last= $model->whereSiteId()
            ->where('news.news_cate_id', $newsCateId)
            ->where('news_description.language_id',$languageId)
            ->where('news.sort','>',$sort)
            ->orderby('news.sort','ASC')
            ->leftjoin('news_description', 'news.news_id', '=','news_description.news_id')
            ->firstHump([
                'news.news_id',
                'news.created_at',
                'news_description.news_title',
                'news_description.news_thumb',
                'news_description.news_desc',
            ]);

        return $last;
    }
    //获取下一条数据
    public static function getNext($newsCateId,$languageId,$sort)
    {
        $model = new NewsModel();
        $next= $model->whereSiteId()
            ->where('news.news_cate_id', $newsCateId)
            ->where('news_description.language_id',$languageId)
            ->where('news.sort','<',$sort)
            ->orderby('news.sort','DESC')
            ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
            ->firstHump([
                'news.news_id',
                'news.created_at',
                'news_description.news_title',
                'news_description.news_thumb',
                'news_description.news_desc',
            ]);

        return $next;
    }


    //获取推荐
    public static function getRecommend($languageId,$number,$tableId,$newsSort=[],$newsCateId=[])
    {
        $model = new NewsModel();
        if(!empty($newsCateId)){
            $model= $model->where('news.news_cate_id',$newsCateId);
        }
        if(!empty($newsSort)){
            $model= $model->orderby('news.news_hit','DESC');
        }
        $news= $model->whereSiteId()
            ->where('news.table_id',$tableId)
            ->where('news.recommend', 1)
            ->where('news_description.language_id',$languageId)
            ->limit($number)
            ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
            ->getHumpArray([
                'news.*',
                'news_description.*',
            ]);

        return $news;
    }

    //获取每套新闻推荐点击量高----学校-站点16
    public static function getAllNews($languageId,$number){
        $res1 = self::getRecommend($languageId,$number,1,0);
        if(isset($res1)){
            foreach ($res1 as $key=>$value){
                $res1[$key]['tableId']=1;
            }
        }
        $res2 = self::getRecommend($languageId,$number,2,0);
        if(isset($res2)){
            foreach ($res2 as $key=>$value){
                $res2[$key]['tableId']=2;
            }
        }
        $res3 = self::getRecommend($languageId,$number,3,0);
        if(isset($res3)){
            foreach ($res3 as $key=>$value){
                $res3[$key]['tableId']=3;
            }
        }
        $res4 = self::getRecommend($languageId,$number,4,0);
        if(isset($res4)){
            foreach ($res4 as $key=>$value){
                $res4[$key]['tableId']=4;
            }
        }
        $res5 = self::getRecommend($languageId,$number,5,0);
        if(isset($res5)){
            foreach ($res5 as $key=>$value){
                $res5[$key]['tableId']=5;
            }
        }
        $res6 = self::getRecommend($languageId,$number,6,0);
        if(isset($res6)){
            foreach ($res6 as $key=>$value){
                $res6[$key]['tableId']=6;
            }
        }
        $res7 = self::getRecommend($languageId,$number,7,0);
        if(isset($res7)){
            foreach ($res7 as $key=>$value){
                $res7[$key]['tableId']=7;
            }
        }
        $res8 = self::getRecommend($languageId,$number,8,0);
        if(isset($res8)){
            foreach ($res8 as $key=>$value){
                $res8[$key]['tableId']=8;
            }
        }
        $res9 = self::getRecommend($languageId,$number,9,0);
        if(isset($res9)){
            foreach ($res9 as $key=>$value){
                $res9[$key]['tableId']=9;
            }
        }
        $res10 = self::getRecommend($languageId,$number,10,0);
        if(isset($res10)){
            foreach ($res10 as $key=>$value){
                $res10[$key]['tableId']=10;
            }
        }
        $arr = array_merge($res1,$res2,$res3,$res4,$res5,$res6,$res7,$res8,$res9,$res10);

        return $arr;
    }


    public static function getMenuName($newsId,$languageId)
    {
        $model = new NewsModel();
        $news = $model->whereSiteId()->where('news.news_id', $newsId)
            ->where('news_description.language_id',$languageId)
            ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
            ->firstHumpArray([
                'news.*',
                'news_description.*',
            ]);
        if(!empty($news)){
            $arr=[];
            $arr[]=$news;
            //获取父亲
            $res = NewsCateLogic::getNewsCateParents($news['newsCateId'],$languageId);
            $arr2=[];
            if(!empty($res)){
                $arr2=array_merge($res,$arr);
            }
            return $arr2;
        }
        return;
    }

    //计算
    public static function newsCount($cateId){
        $model = new NewsModel();
        $count = $model->whereSiteId()
            ->whereIn('news_cate_id', $cateId)
            ->count();
        return $count;
    }

    //网站的所有新闻列表
    public static function getNewsByNameList($languageId,$data)
    {
        $model = new NewsModel();
        $newsTitle = '%' . $data['newsTitle'] . '%';
        $newsLists = $model->whereSiteId()
            ->where('news_description.language_id',$languageId)
            ->where('news_description.news_title', 'like', $newsTitle)
            ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
            ->select([
                'news.*',
                'news_description.*',
            ]);
        $res=$newsLists->getDdvPageHumpArray(true);
        /*if(isset($res)){
            foreach ($res['lists'] as $key=>$value){
                $name=NewsCateLogic::getNewsCateName($value['newsCateId'],$value['languageId']);
                $res['lists'][$key]['newsCateTitle']=empty($name) ? '' : $name;
            }
        }*/
        return $res;
    }

    //获取导航banner使用
    public static function getMenuBanner($newsId,$languageId)
    {
        $model = new NewsModel();
        $product = $model->whereSiteId()->where('news.news_id', $newsId)
            ->where('news_description.language_id',$languageId)
            ->leftjoin('news_description', 'news.news_id', '=', 'news_description.news_id')
            ->firstHumpArray(['news.*','news_description.*']);

        return $product;
    }


    //优化代码--单条
    public static function getNewsOne($newsId)
    {
        $model=new NewsModel();
        $news=$model->whereSiteId()->where('news_id',$newsId)->firstHump();
        $news->newsThumb=[];
        $news->newsbanner=$news->banner()->getHump(['sort','newsBannerPic']);
        $news->lang=$news->desc()->getHump();
        foreach ($news->lang as $key=>$value){
            $news->newsThumb=$value['newsThumb'];
            $news->lang[$key]['photos']=$news->photos()->where('language_id',$value['languageId'])->getHump(['newsImagePic','newsImageDesc','languageId']);
        }
        return $news;

    }
    //优化代码--列表
    public static function getNewsList($data,$tableId)
    {
        $model=new NewsModel();
        if(!empty($data['newsTitle']) ){
            $model = $model->where('news_description.news_title','like', '%'. $data['newsTitle'] . '%');
        }
        if(!empty($data['languageId']) ){
            $model = $model->where('news_description.language_id',$data['languageId']);
        }
        if(isset($data['newsCateId'])){
            $cateId=NewsCateLogic::getCateId($data['newsCateId'],$tableId);
            $model = $model->whereIn('news.news_cate_id',$cateId);
        }
        $sort='sort';
        if(isset($data['newsSort'])){
            $sort='news_hit';
        }
        $news=$model->whereSiteId()
            ->where('table_id',$tableId)
            ->orderby('news.'.$sort,'DESC')
            ->leftjoin('news_description','news.news_id', '=', 'news_description.news_id')
            ->getDdvPageHumpArray();
        if(isset($news)){
            $settlesRes = array();
            foreach ($news['lists'] as $key=>$value){
                $name=NewsCateLogic::getNewsCateName($value['newsCateId'],$value['languageId']);
                $news['lists'][$key]['newsCateTitle']=empty($name) ? '' : $name;
                if(isset($data['nameSort'])){
                    $nameFirstChar = NameSortLogic::getFirstCharter($value['newsTitle']);
                    $settlesRes[] = $nameFirstChar;//以这个首字母作为key
                }
            }
            if(isset($data['nameSort'])){
                array_multisort($settlesRes, SORT_ASC, $news['lists']);
            }
        }
        return $news;
    }







}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Product;

use App\Logic\Gallery\GalleryLogic;
use App\Logic\Teacher\TeacherLogic;
use App\Model\Product\ProductCateDescModel;
use App\Model\Product\ProductCateModel;
use App\Model\Product\ProductModel;
use App\Model\Product\ProductDescModel;
use App\Model\Product\ProductimageModel;
use App\Model\Product\ProductBannerModel;
use App\Model\Product\ProductAttributeModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class ProductLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'productCateId' => $data['productCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'isOn' => $data['isOn'],
            'tableId'=>$tableId,
            'productNumber'=>empty($data['productNumber']) ? '' : $data['productNumber']
        ];
        self::addAffair($main,$data);
    }

    /*
     * 产品的模糊搜索
     */
    public static function getProductSearch($languageId, $data) {
        $productDescModel = new ProductDescModel();

        if (!empty($data['productTitle'])) {

            $whereLike = '%'.$data['productTitle'].'%';

        } elseif(empty($data['productTitle'])) {

            $whereLike = '';
        }

        $productLists = $productDescModel
            ->where('product_description.language_id',$languageId)
            ->where('product_description.product_title', 'like', $whereLike)
            ->getDdvPageHumpArray();

        if (empty($productLists['lists'])) {//没找到，返回空
            return $productLists;
        }

        foreach ($productLists['lists'] as $k => $v) {
            $id[] = $v['productId'];
        }

        $productAttributeModel = new ProductAttributeModel();
        $res = $productAttributeModel
            ->whereIn('product_id', $id)
            ->getHumpArray(['language_id', 'attribute_name', 'attribute_content', 'product_id']);

        if (empty($res)) {//没找到，返回空

            return $res;
        }

        foreach ($res as $k1 => $v1) {

            $new_all[$v1['productId']][] = $v1;
        }
        foreach ($productLists['lists'] as $k2 => $v2) {

            $productLists['lists'][$k2]['attribute'] = $new_all[$v2['productId']];
        }
        return $productLists;
    }


    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['productThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['productThumb']);
            }
            $productId=self::addProduct($main,$data['tableId']);
            if(!empty($data['teacher'])){
                TeacherLogic::addTeacherToClass($data['teacher'],$productId);
            }
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'productId' => $productId,
                    'galleryId' => $GalleryId,
                    'productTitle' => empty($value['productTitle']) ? '' : $value['productTitle'],
                    'productDesc' => empty($value['productDesc']) ? '' : $value['productDesc'],
                    'productContent' => empty($value['productContent']) ? '' : $value['productContent'],
                    'productThumb' => empty($data['productThumb']) ? '' : $data['productThumb'],
                    'productOldPrice' => empty($data['productOldPrice']) ? '0' : $data['productOldPrice'],
                    'productSalePrice' => empty($data['productSalePrice']) ? '0' : $data['productSalePrice'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addProductDesc($desc);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //添加广告图片
            if(isset($data['probanner'])){
                self::addProductBanner($data['probanner'],$productId);
            }
            //添加图片
            if(isset($data['lang'])){
                foreach ($data['lang'] as $key=>$value){
                    //多图
                    if(!empty($value['photos'])){
                        self::addProductimage($value['photos'],$productId);
                    }
                    //属性
                    if(isset($value['attribute'])){
                        self::addProductAttribute($value['attribute'],$productId);
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
    public static function addProduct ($data=[],$tableId)
    {
        $model = new ProductModel();
        //检查排序
        self::Sort($data['productCateId'],$data['sort'],$tableId);
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //查排序是否唯一
    public static function Sort ($productCateId,$sort,$tableId)
    {
        $model = new ProductModel(/*$prefix*/);
        $res = $model->whereSiteId()->where('table_id',$tableId)->where('product_cate_id',$productCateId)->where('sort',$sort)->firstHump(['*']);
        $sort = $model->whereSiteId()->where('table_id',$tableId)->where('product_cate_id',$productCateId)->orderby('sort','DESC')->firstHump(['sort']);
        if(!empty($res)){
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'PRODUCT_SORT');
        }
    }

    //添加详细表
    public static function addProductDesc ($data=[])
    {
        $model = new ProductDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //添加属性表
    public static function addProductAttribute($data=[],$productId)
    {
        foreach ($data as $key => $value){
            $Productattribute = [
                'productId' => $productId,
                'attributeName' => empty($value['attributeName']) ? '' : $value['attributeName'],
                'attributeContent' => empty($value['attributeContent']) ? '' : $value['attributeContent'],
                'languageId'=>$value['languageId'],
            ];
            $model = new ProductAttributeModel();
            $model->setDataByHumpArray($Productattribute)->save();
        }

    }

    //添加图片表
    public static function addProductimage ($data=[],$ProductId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['productImagePic']);
            $Productimage = [
                'productId' => $ProductId,
                'galleryId' => $GalleryId,
                'productImagePic' => $value['productImagePic'],
                'productImageDesc' => empty($value['productImageDesc']) ? '' : $value['productImageDesc'],
                'languageId'=>$value['languageId'],
            ];
            $model = new ProductimageModel();
            $model->setDataByHumpArray($Productimage)->save();
        }

    }

    //添加广告图
    public static function addProductBanner ($data=[],$ProductId)
    {
        foreach ($data as $key => $value){
            $GalleryId = GalleryLogic::getGalleryId($value['productBannerPic']);
            $ProductBanner = [
                'productId' => $ProductId,
                'galleryId' => $GalleryId,
                'productBannerPic' => $value['productBannerPic'],
                'sort'=>empty($value['sort']) ? 1 : $value['sort']
            ];
            $model = new ProductBannerModel();
            $model->setDataByHumpArray($ProductBanner)->save();
        }
    }

    //获取列表
    public static function getProductList($data,$productType)
    {
        $model = new ProductModel();
        //分类
        $where=[];
        $name=[];
        if(isset($data['languageId'])){
            $name='product_description.language_id';
            $where=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='product.is_on';
            $show=$data['isOn'];
        }
        if (isset($data['productTitle'])) {
            $ProductTitle = '%' . $data['productTitle'] . '%';
        } else {
            $ProductTitle = '%';
        }
        //分类名称搜索
        $productId=[];
        $proCateId=[];
        if(isset($data['productCateTitle'])){
            $productId='product.product_cate_id';
            $proCateId=ProductCateLogic::getProCateId($data['productCateTitle'],$productType);
        }
        if(isset($data['sort'])){
            $sort='created_at';
        }else{
            $sort='sort';
        }
        if(isset($data['productCateId'])){
            $name3='product.product_cate_id';
            $cateId=ProductCateLogic::getCateId($data['productCateId'],$productType);
            $ProductLists = $model->whereSiteId()
                ->where('table_id',$productType)
                ->where($name,$where)
                ->where($showName,$show)
                ->whereIn($name3,$cateId)
                ->where($productId,$proCateId)
                ->where('product_description.product_title', 'like', $ProductTitle)
                ->orderby('product.'.$sort,'DESC')
                ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
                ->select([
                    'product.*',
                    'product_description.*',
                ]);
        }else{
            $ProductLists = $model->whereSiteId()
                ->where('table_id',$productType)
                ->where($name,$where)
                ->where($showName,$show)
                ->where($productId,$proCateId)
                ->where('product_description.product_title', 'like', $ProductTitle)
                ->orderby('product.'.$sort,'DESC')
                ->leftjoin('product_description', 'product.product_id', '=','product_description.product_id')
                ->select([
                    'product.*',
                    'product_description.*',
                ]);
        }
        $res = $ProductLists->getDdvPageHumpArray(true);
        if(isset($res['lists'])){
            foreach ($res['lists'] as $key=>$value){
                //分类名称
                $name=ProductCateLogic::getProductCateName($value['productCateId'],$value['languageId']);
                //属性
                $attribute=self::getAttribute($value['productId'],$value['languageId']);

                $res['lists'][$key]['productCateTitle']=empty($name) ? '' : $name;
                $res['lists'][$key]['attribute']=empty($attribute) ? [] : $attribute;
            }
        }
        return $res;
    }

    //获取住单条
    public static function getProductOne($ProductId,$tableId)
    {
        $model = new ProductModel();
        $Product = $model->whereSiteId()
            ->where('table_id',$tableId)
            ->where('product_id', $ProductId)
            ->firstHump(['*']);
        if(isset($Product)){
            $ProductDesc=self::getProductDesc($Product['productId']);
            if(!empty($ProductDesc)){
                foreach ($ProductDesc as $key=>$value){
                    $Product['productThumb']=empty($value['productThumb']) ? '' : $value['productThumb'];
                    $Product['productOldPrice']=empty($value['productOldPrice']) ? '' : $value['productOldPrice'];
                    $Product['productSalePrice']=empty($value['productSalePrice']) ? '' : $value['productSalePrice'];
                    //属性
                    $attribute=self::getAttribute($Product['productId'],$value['languageId']);
                    $value['attribute']=empty($attribute) ? [] : $attribute;
                    //图片
                    $image = self::getImage($Product['productId'],$value['languageId']);
                    $value['photos']=empty($image) ? [] : $image;
                }
                //广告图
                $banner = self::getBanner($ProductId);
                $Product['probanner']=empty($banner) ? [] : $banner;
                //获取教师
                $teacher = TeacherLogic::getTeacherByClassId($ProductId);
                $Product['teacher']=empty($teacher) ? [] : $teacher;
                $Product['lang']=empty($ProductDesc) ? [] : $ProductDesc;
            }
        }
        return $Product;
    }
    //获取详情全部
    public static function getProductDesc($productId)
    {
        $model = new ProductDescModel();
        $ProductDeac = $model->where('product_id', $productId)->getHump(['*']);
        return $ProductDeac;
    }

    public static function getCateProduct($productCateId,$productType)
    {
        $model = new ProductModel();
        $Product = $model->where('table_id',$productType)->where('product_cate_id', $productCateId)
            ->firstHump(['*']);
        return $Product;
    }

    //获取属性
    public static function getAttribute($productId,$languageId)
    {
        $model = new ProductAttributeModel();
        $attribute=$model->where('product_id',$productId)
            ->where('language_id',$languageId)
            ->getHump(['attribute_name','attribute_content','language_id']);
        return $attribute;
    }

    //获取图片
    public static function getImage($productId,$languageId)
    {
        $model = new ProductimageModel();
        $image=$model->where('product_id',$productId)
            ->where('language_id',$languageId)
            ->getHump(['product_image_pic','product_image_desc','language_id']);
        return $image;
    }

    //广告图
    public static function getBanner($productId)
    {
        $model = new ProductBannerModel();
        $image=$model->where('product_id',$productId)
            ->orderBy('sort','DESC')
            ->getHump(['product_banner_pic','sort']);
        return $image;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'productCateId' => $data['productCateId'],
            'sort' => $data['sort'],
            'recommend' => $data['recommend'],
            'isOn' => $data['isOn'],
            'productNumber' =>empty($data['productNumber']) ? '' : $data['productNumber']
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['productThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['productThumb']);
            }
            $productId=$data['productId'];
            if(!empty($data['teacher'])){
                //先删除在添加
                TeacherLogic::deleteTeacherToClass($productId);
                TeacherLogic::addTeacherToClass($data['teacher'],$productId);
            }
            self::editProduct($main,$productId,$data['tableId']);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'galleryId'=>$GalleryId,
                    'productTitle' => empty($value['productTitle']) ? '' : $value['productTitle'],
                    'productDesc' => empty($value['productDesc']) ? '' : $value['productDesc'],
                    'productContent' => empty($value['productContent']) ? '' : $value['productContent'],
                    'productThumb' => empty($data['productThumb']) ? '' : $data['productThumb'],
                    'productOldPrice' => empty($data['productOldPrice']) ? '0' : $data['productOldPrice'],
                    'productSalePrice' => empty($data['productSalePrice']) ? '0' : $data['productSalePrice'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editProductDesc($desc,$productId,$value['languageId']);
                //编辑器里面的文件
                if(isset($value['contentFile'])){
                    foreach ($value['contentFile'] as $item){
                        GalleryLogic::getGalleryId($item['galleryUrl']);
                    }
                }
            }
            //删除属性
            self::editProductAttribute($productId);
            //删除图片
            self::editProductimage($productId);
            //删除广告
            self::editProductBanner($productId);
            //添加广告图片
            if(isset($data['probanner'])){
                self::addProductBanner($data['probanner'],$productId);
            }
            if(isset($data['lang'])){
                foreach ($data['lang'] as $key=>$value){
                    if(isset($value['attribute'])){
                        //添加属性
                        self::addProductAttribute($value['attribute'],$productId);
                    }
                    if(isset($value['photos'])){
                        //添加图片
                        self::addProductimage($value['photos'],$productId);
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
    public static function editProduct($data=[],$productId,$tableId)
    {
        $model = new ProductModel();
        self::SortTwo($data['productCateId'],$data['sort'],$productId,$tableId);
        $model->where('product_id', $productId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$productId)
    {
        $model = new ProductModel();
        $model->where('product_id', $productId)->updateByHump($data);

    }

    //查排序是否唯一
    public static function SortTwo ($productCateId,$sort,$productId,$tableId)
    {
        $model = new ProductModel();
        $res = $model->whereSiteId()->where('table_id',$tableId)->where('product_cate_id',$productCateId)->where('product_id','<>',$productId)->where('sort',$sort)->firstHump(['*']);
        $sort = $model->whereSiteId()->where('table_id',$tableId)->where('product_cate_id',$productCateId)->orderby('sort','DESC')->firstHump(['sort']);
        if(!empty($res)){
            throw new RJsonError("排序重复,目前排序为:"."$sort", 'PRODUCT_SORT');
        }
    }

    //编辑详细表
    public static function editProductDesc($data=[],$productId,$languageId)
    {
        $model = new ProductDescModel();
        $model->where('product_id', $productId)->where('language_id', $languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($productId)
    {
        \DB::beginTransaction();
        try{
            self::deleteProduct($productId);
            self::deleteProductDesc($productId);
            self::editProductAttribute($productId);
            self::editProductimage($productId);
            self::editProductBanner($productId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteProduct($productId)
    {
        $model = new ProductModel();
        $model->where('product_id', $productId)->delete();
    }
    //删除详
    public static function deleteProductDesc($productId)
    {
        $model = new ProductDescModel();
        $model->where('product_id', $productId)->delete();
    }

    //属性删、该
    public static function editProductAttribute($productId)
    {
        //查是否有属性
        $attribute = self::getAllAttribute($productId);
        if(isset($attribute)){
            //有把它删除
            self::deleteProductAttribute($productId);
        }
    }

    //图片删、该
    public static function editProductimage($productId)
    {
        //查是否有图片
        $image = self::getAllImage($productId);
        if(isset($image)){
            //有图片把它删除
            self::deleteProductImage($productId);
        }
    }

    //广告删、该
    public static function editProductBanner($productId)
    {
        //查是否有图片
        $image = self::getAllBanner($productId);
        if(isset($image)){
            //有图片把它删除
            self::deleteProductBanner($productId);
        }
    }

    //获取属性
    public static function getAllAttribute($productId)
    {
        $model = new ProductAttributeModel();
        $attribute=$model->where('product_id',$productId)
            ->getHump(['attribute_name','attribute_content','language_id']);
        return $attribute;
    }

    //获取图片
    public static function getAllImage($productId)
    {
        $model = new ProductimageModel();
        $image=$model->where('product_id',$productId)
            ->getHump(['product_image_pic','product_image_desc','language_id']);
        return $image;
    }

    //获取广告
    public static function getAllBanner($productId)
    {
        $model = new ProductBannerModel();
        $banner=$model->where('product_id',$productId)
            ->orderBy('sort','DESC')
            ->getHump(['product_banner_pic','sort']);
        return $banner;
    }

    //删除属性
    public static function deleteProductAttribute($productId)
    {
        $model = new ProductAttributeModel();
        $model->where('product_id', $productId)->delete();
    }

    //删除图片
    public static function deleteProductImage($productId)
    {
        $model = new ProductimageModel();
        $model->where('product_id', $productId)->delete();
    }

    //删除广告图
    public static function deleteProductBanner($productId)
    {
        $model = new ProductBannerModel();
        $model->where('product_id', $productId)->delete();
    }


    //=========================前端调用单条==============================

    //查单条
    public static function getProduct($productId,$languageId)
    {
        $model = new ProductModel();
        $product = $model->whereSiteId()
            ->where('product.product_id', $productId)
            ->where('product_description.language_id',$languageId)
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->leftjoin('product_category_description', 'product.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->firstHump([
                'product.*',
                'product_description.*',
                'product_category_description.product_cate_title',
            ]);
        if(isset($product)){
            //属性
            $attribute=self::getAttribute($product['productId'],$languageId);
            $product['attribute']=empty($attribute) ? [] : $attribute;
            //图片
            $image = self::getImage($product['productId'],$languageId);
            $product['photos']=empty($image) ? [] : $image;
            //广告
            $image = self::getBanner($product['productId']);
            $product['probanner']=empty($image) ? [] : $image;
            //获取教师
            $teacher = TeacherLogic::getTeacherApiByClassId($product['productId'],$languageId);
            $Product['teacher']=empty($teacher) ? [] : $teacher;
            //点击量
            $hit['productHit']=$product['productHit']+1;
            self::Click($hit,$product['productId']);
        }


        return $product;
    }

    //点击量
    public static function Click($data=[],$productId)
    {

        $model = new ProductModel();
        $model->where('product_id', $productId)->updateByHump($data);
    }

    //获取上一条数据
    public static function getLast($productCateId,$languageId,$sort)
    {
        $model = new ProductModel();
        $last= $model->whereSiteId()
            ->where('product.product_cate_id', $productCateId)
            ->where('product_description.language_id',$languageId)
            ->where('product.sort','>',$sort)
            ->orderby('product.sort','ASC')
            ->leftjoin('product_description','product.product_id', '=', 'product_description.product_id')
            ->firstHump([
                'product.product_id',
                'product.created_at',
                'product_description.product_title',
                'product_description.product_thumb',
                'product_description.product_desc',
            ]);
        return $last;
    }
    //获取下一条数据
    public static function getNext($productCateId,$languageId,$sort)
    {
        $model = new ProductModel();
        $next= $model->whereSiteId()
            ->where('product.product_cate_id', $productCateId)
            ->where('product_description.language_id',$languageId)
            ->where('product.sort','<',$sort)
            ->orderby('product.sort','DESC')
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->firstHump([
                'product.product_id',
                'product.created_at',
                'product_description.product_title',
                'product_description.product_thumb',
                'product_description.product_desc',
            ]);
        return $next;
    }

    //获取推荐
    public static function getRecommend($languageId,$number,$tableId,$productCateId,$api=[])
    {
        $model = new ProductModel();
        $model = $model->whereSiteId();
        $model = $model->where('product.table_id',$tableId);
        $model = $model->where('product.recommend', 1);
        if($productCateId !=0){
            $pid = self::getProductId($productCateId);
            //$ids =join(',', $pid);
            $model->whereIn('product.product_cate_id',$pid);
        }
        $model = $model->where('product.is_on', 1);
        $model = $model->where('product_description.language_id',$languageId);
        $res= $model->limit($number)
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->getHumpArray([
                'product.*',
                'product_description.*',
            ]);
        if(!empty($res) && $api==1){
            foreach ($res as $key=>$value){
                //属性
                $attribute=self::getAttribute($value['productId'],$languageId);
                $res[$key]['attribute']=empty($attribute) ? [] : $attribute;
            }
        }
        return $res;
    }

    /*
     * 找子类id的一个方法
     */
    public static function getProductId($cateId, $ids=array()) {

        $model = new ProductCateModel();
        $ids[] = (int)$cateId;
        $res = $model->where('pid', $cateId)->getHumpArray(['product_cate_id', 'pid']);
        if ($res) {
            foreach ($res as $k => $v) {

               $ids = self::getProductId($v['productCateId'], $ids);
            }
        } else {
            return $ids;
        }
            return $ids;
    }




    //获取导航用
    public static function getMenuName($productId,$languageId)
    {
        $model = new ProductModel();
        $product = $model->whereSiteId()->where('product.product_id', $productId)
            ->where('product_description.language_id',$languageId)
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->firstHumpArray(['product.*','product_description.*']);
        if(!empty($product)){
            $arr=[];
            $arr[]=$product;
            //获取父亲
            $res = ProductCateLogic::getProductCateParents($product['productCateId'],$languageId);
            $arr2=[];
            if(!empty($res)){
                $arr2=array_merge($res,$arr);
            }
            return $arr2;
        }

        return;
    }

    //计算
    public static function productCount($cateId){
        $model = new ProductModel();
        $count = $model->whereSiteId()
            ->whereIn('product.product_cate_id', $cateId)
            ->count();
        return $count;
    }

    //获取导航banner使用
    public static function getMenuBanner($productId,$languageId)
    {
        $model = new ProductModel();
        $product = $model->whereSiteId()->where('product.product_id', $productId)
            ->where('product_description.language_id',$languageId)
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->firstHumpArray(['product.*','product_description.*']);

        return $product;
    }

    //获取分类下的所有产品
    public static function getProductByCateId($productCateId,$number,$languageId)
    {
        $model = new ProductModel();
        $product = $model->whereSiteId()->where('product.product_cate_id', $productCateId)
            ->where('product_description.language_id',$languageId)
            ->where('product.is_on',1)
            ->limit($number)
            ->leftjoin('product_description', 'product.product_id', '=', 'product_description.product_id')
            ->getHumpArray(['product.*','product_description.*']);
        return $product;
    }


}
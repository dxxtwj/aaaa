<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Product;

use App\Logic\Common\TreeLogic;
use App\Model\Product\ProductCateModel;
use App\Model\Product\ProductCateDescModel;
use App\Logic\Menu\MenuLogic;
use App\Http\Middleware\SiteId;
use App\Logic\Product\ProductLogic;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use PhpParser\Node\Expr\Empty_;
use function var_dump;

class ProductCateLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $tableId=$data['tableId'];
        $main=[
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'sort'=>$data['sort'],
            'recommend'=>$data['recommend'] ?? 0,
            'tableId'=>$tableId
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $productCateId=self::addProductCate($main);
            foreach ($data['lang'] as $key=>$value ){
                $desc=[
                    'productCateId' => $productCateId,
                    'productCateTitle' => $value['productCateTitle'],
                    'productCateDesc' => $value['productCateDesc'] ?? '',
                    'productCateImage' => empty($data['productCateImage']) ? '' : $data['productCateImage'],
                    'recommendImage' => empty($data['recommendImage']) ? '' : $data['recommendImage'],
                    'languageId'=>$value['languageId'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::addProductCateDesc($desc);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addProductCate ($data=[])
    {
        $model = new ProductCateModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addProductCateDesc ($data=[])
    {
        $model = new ProductCateDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }


    //获取新闻分类列表
    public static function getProductCateList($data=[],$tableId)
    {
        $model = new ProductCateModel();
        $name=[];
        $where=[];
        $name1=[];
        $where1=[];
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='product_category.is_on';
            $show=$data['isOn'];
        }
        if(isset($data['languageId'])){
            $name='product_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1 ='product_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }

        $ProductLists =$model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->where($showName,$show)
            ->leftjoin('product_category_description','product_category.product_cate_id', '=','product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.product_cate_id',
                'product_category.pid',
                'product_category.sort',
                'product_category.site_id',
                'product_category.is_on',
                'product_category_description.product_cate_id',
                'product_category_description.language_id',
                'product_category_description.product_cate_title',
                'product_category_description.product_cate_image',
                'product_category_description.product_cate_desc',
                'product_category_description.site_title',
                'product_category_description.site_keywords',
                'product_category_description.site_description',

            ]);
        $Product = TreeLogic::Producttree($ProductLists);
        return $Product;
    }

    //菜单地址用
    public static function getCateLists($data=[],$tableId)
    {

        $model = new ProductCateModel();

        $name=[];
        $where=[];
        $name1=[];
        $where1=[];
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='product_category.is_on';
            $show=$data['isOn'];
        }
        if(isset($data['languageId'])){
            $name='product_category_description.language_id';
            $where=$data['languageId'];
        }else{
            $name1 ='product_category_description.language_id';
            $languageId = SiteId::getLanguageId();
            $where1 =$languageId;
        }
        $ProductLists =$model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where($name,$where)
            ->where($name1,$where1)
            ->where($showName,$show)
            ->leftjoin('product_category_description','product_category.product_cate_id', '=','product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.pid',
                'product_category.product_cate_id',
                'product_category_description.product_cate_title',
            ]);
        if(isset($ProductLists)){
            foreach ($ProductLists as $key=>$value){
                $ProductLists[$key]['propId']='productCateId';
                $ProductLists[$key]['propTitle']='productCateTitle';
            }
        }
        $Product = TreeLogic::Producttree($ProductLists);
        return $Product;
    }


    //获取主单条
    public static function getProductCateOne($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $Product = $model->where('product_cate_id', $productCateId)
            ->firstHump(['*']);
        if(isset($Product)){
            $ProductCateDesc = self::getProductCateDesc($Product['productCateId']);
            if(isset($ProductCateDesc)){
                foreach ($ProductCateDesc as $key=>$value){
                    $Product['productCateImage']=empty($value['productCateImage']) ? '' : $value['productCateImage'];
                    $Product['recommendImage']=empty($value['recommendImage']) ? '' : $value['recommendImage'];
                }
                $Product['lang']=empty($ProductCateDesc) ? [] : $ProductCateDesc;
            }
        }
        return $Product;
    }

    //获取详情全部
    public static function getProductCateDesc($productCateId)
    {
        $model = new ProductCateDescModel();
        $ProductCateDesc = $model->where('product_cate_id', $productCateId)->getHump(['*']);
        return $ProductCateDesc;
    }

    //获取分类名称
    public static function getProductCateName($productCateId,$languageId)
    {
        $model = new ProductCateDescModel();
        $ProductCateDesc = $model->where('product_cate_id', $productCateId)->where('language_id',$languageId)->firstHump(['product_cate_title']);
        return $ProductCateDesc->productCateTitle;
    }

    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'productCateId' => $data['productCateId'],
            'isOn' => $data['isOn'],
            'sort'=>$data['sort'],
            'recommend'=>$data['recommend'] ?? 0
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $productCateId=$data['productCateId'];
            self::editProductCate($main,$productCateId,$data['tableId']);
            foreach ($data['lang'] as $key=>$value){
                $desc=[
                    'productCateTitle' => $value['productCateTitle'],
                    'productCateDesc' => $value['productCateDesc'] ?? '',
                    'productCateImage' => empty($data['productCateImage']) ? '' : $data['productCateImage'],
                    'recommendImage' => empty($data['recommendImage']) ? '' : $data['recommendImage'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editProductCateDesc($desc,$productCateId,$value['languageId']);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editProductCate($data=[],$productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $model->where('table_id',$tableId)->where('product_cate_id', $productCateId)->updateByHump($data);

    }

    //是否显示
    public static function isShow($data=[],$productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $model->where('product_cate_id', $productCateId)->updateByHump($data);

    }

    //编辑详细表
    public static function editProductCateDesc($data=[],$productCateId,$languageId)
    {
        $model = new ProductCateDescModel();
        $model->where('product_cate_id', $productCateId)->where('language_id',$languageId)->updateByHump($data);
    }

    //删除事务
    public static function delAffair($productCateId,$tableId)
    {
        \DB::beginTransaction();
        try{
            self::deleteProductCate($productCateId,$tableId);
            self::deleteProductCateDesc($productCateId,$tableId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //删除主
    public static function deleteProductCate($productCateId,$tableId)
    {
        $Product = ProductLogic::getCateProduct($productCateId,$tableId);
        if (!empty($Product)){
            throw new RJsonError('该类下还有数据', 'DELETE_PRODUCTCATE');
        }
        $cate = self::getChildId($productCateId,$tableId);
        if (isset($cate)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_PRODUCT_CATE');
        }

        $model = new ProductCateModel();
        $model->where('product_cate_id', $productCateId)->delete();
    }
    //删除详
    public static function deleteProductCateDesc($productCateId,$tableId)
    {
        $model = new ProductCateDescModel();
        $model->where('product_cate_id', $productCateId)->delete();
    }


    //获取上一级ID
    public static function getProductCateId($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $product = $model->where('product_category.product_cate_id', $productCateId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->firstHumpArray([
                'product_category.pid',
                'product_category.table_id'
            ]);
        return $product;

    }

    //获取下一级
    public static function getChildId($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $Product = $model->where('product_category.pid', $productCateId)
            ->leftjoin('product_category_description','product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->firstHump([
                'product_category.product_cate_id'
            ]);
        return $Product;

    }

    //获取子类
    public static function productCate($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $arr=[];
        $newsLists = $model->whereSiteId()->where('table_id',$tableId)->getHumpArray(['pid','productCateId']);
        $newsCate = TreeLogic::ProSubs($newsLists,$productCateId);
        if(!empty($newsCate)){
            foreach ($newsCate as $key=>$value){
                $arr[]=$value['productCateId'];
                if(!empty($value['child'])){
                    foreach ($value['child'] as $val) {
                        $arr[]=$val['productCateId'];
                        if($value['child']){
                            foreach ($val['child'] as $v) {
                                $arr[]=$v['productCateId'];
                            }
                        }
                    }
                }
            }
        }
        $productCateId=(int) $productCateId;
        array_push($arr,$productCateId);
        return $arr;
    }

    //获取类下的所有子类ID
    public static function getCateId($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $data = $model->whereSiteId()->where('table_id',$tableId)->where('pid',$productCateId)->getHumpArray(['pid','productCateId']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['productCateId'];
            }
        }
        $arr2 = self::productTree($arr,$tableId);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $productCateId=(int) $productCateId;
        array_push($res,$productCateId);
        return $res;
    }
    public static function productTree($productCateId,$tableId)
    {
        $model = new ProductCateModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereSiteId()->where('table_id',$tableId)->whereIn('pid',$productCateId)->getHumpArray(['pid','productCateId']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['productCateId'];
                $arr2 =self::productTree($arr,$tableId);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }

    //搜索
    public static function getProCateId($proCateName,$tableId)
    {
        if (isset($proCateName)) {
            $proCateName = '%' . $proCateName . '%';
        }
        $model = new ProductCateModel();
        $product = $model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where('product_category_description.product_cate_title', 'like', $proCateName)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->firstHumpArray([
                'product_category.search_num',
                'product_category_description.product_cate_id',
            ]);
        self::putSearchNumber($product);
        return $product['productCateId'];

    }

    //修改搜索次数量
    public static function putSearchNumber($product)
    {
        $data['searchNum']=$product['searchNum']+1;
        $model = new ProductCateModel();
        $model->where('product_cate_id', $product['productCateId'])->updateByHump($data);
    }

    //=========================前端调用单条==============================

    //查单条
    public static function getProductCate($productCateId,$languageId)
    {
        $model = new ProductCateModel();
        $product = $model->where('product_category.product_cate_id', $productCateId)
            ->where('product_category_description.language_id',$languageId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->firstHumpArray([
                'product_category.*',
                'product_category_description.*',
            ]);
        return $product;
    }

    //获取名称
    public static function getCalssName($tableId,$productNumber,$languageId)
    {

        $model = new ProductCateModel();
        $product = $model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where('product_category_description.language_id',$languageId)
            ->where('product_category.is_on', 1)
            ->orderby('product_category.search_num','DESC')
            ->limit($productNumber)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->getHump([
                'product_category_description.product_cate_title',
                'product_category_description.recommend_image',
                'product_category_description.product_cate_id',
            ]);
        return $product;
    }

    //获取面包宵
    public static function getProductCateParents($productCateId,$languageId)
    {
        $model = new ProductCateModel();
        $product = $model->whereSiteId()
            ->where('product_category_description.language_id',$languageId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.*',
                'product_category_description.*',
            ]);
        $res = TreeLogic::getProductParents($product,$productCateId);
        if(!empty($res)){
            $arr=[];
            foreach ($res as $value) {
                if($value['pid']==0){
                    $res2 = MenuLogic::getMenuNameByClassId($value['productCateId'],$languageId,1);
                    if(empty($res2)){
                        //如果不存在，去找同级的导航
                        $res2 = self::getMenuNameByBrother($value['tableId'],$languageId);
                        $check=$res2['check'];
                        unset($res2['check']);
                    }else{
                        //检测是否有相同页面的
                        $check=MenuLogic::getMenuCheckByClassId($value['productCateId'],$languageId,1);
                    }
                }
                if(!empty($check)){
                    if($check > 1){
                        $arr[]=$value;
                    }else{
                        if($value['pid']!=0){
                            $arr[]=$value;
                        }
                    }
                }else{
                    $arr[]=$value;
                }
            }
            if(empty($res2)){
                $pro = self::getProductCate($productCateId,$languageId);
                $urlName='product';
                $menu = MenuLogic::getMenuIdByUrl($urlName,$pro['tableId'],$languageId);
                $res2[] = $menu;
            }
            $arr2 = array_merge($res2,$arr);
            return $arr2;
        }else{
            return;
        }

    }
    public static function getMenuNameByBrother($tableId,$languageId)
    {
        $res = self::getParents($tableId,$languageId);
        foreach ($res as $value){
            $res2 = MenuLogic::getMenuByClassId($value['productCateId'],$languageId,1);
            $res2['check']=2;
            return $res2;
        }
        return;
    }

    //获取类下所有子类，并统计每个类下产品的条数、推荐
    public static function getProductCateKids($productCateId,$tableId,$languageId,$recommend)
    {
        $model = new ProductCateModel();
        if($recommend == 1){
            $model = $model->where('product_category.recommend',1);
        }
        $product = $model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where('product_category.pid',$productCateId)
            ->where('product_category_description.language_id',$languageId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.*',
                'product_category_description.*',
            ]);
        if(!empty($product)){
            foreach ($product as $key=>$value){
                $cateId=self::getCateId($value['productCateId'],$tableId);
                $count = ProductLogic::productCount($cateId);
                $product[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['productCateId'],$tableId);
                if(!empty($res)){
                    $product[$key]['kids']=true;
                }else{
                    $product[$key]['kids']=false;
                }
            }
        }
        return $product;
    }


    //获取父级，并统计每个类下产品的条数
    public static function getParents($tableId,$languageId)
    {
        $model = new ProductCateModel();
        $product = $model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where('product_category.pid',0)
            ->where('product_category_description.language_id',$languageId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.*',
                'product_category_description.*',
            ]);
        if(!empty($product)){
            foreach ($product as $key=>$value){
                $cateId=self::getCateId($value['productCateId'],$tableId);
                $count = ProductLogic::productCount($cateId);
                $product[$key]['count'] =empty($count) ? 0 : $count;
                $res = self::getChildId($value['productCateId'],$tableId);
                if(!empty($res)){
                    $product[$key]['kids']=true;
                }else{
                    $product[$key]['kids']=false;
                }
            }
        }
        return $product;
    }

    //menu->banner
    public static function getProductCateParent($productCateId,$languageId)
    {
        $model = new ProductCateModel();
        $product = $model->whereSiteId()
            ->where('product_category_description.language_id',$languageId)
            ->leftjoin('product_category_description', 'product_category.product_cate_id', '=', 'product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.*',
                'product_category_description.*',
            ]);
        $res2 = TreeLogic::getProductParents($product,$productCateId);
        if(!empty($res2)){
            foreach ($res2 as $key=>$value){
              $arr = MenuLogic::getMenuBannerByClassId($value['productCateId'],$languageId,1);
              if(!empty($arr)){
                  $arr2 = MenuLogic::getMenuBanner($arr['menuId'],$languageId);
                  if(!empty($arr2)){
                      return $arr2;
                  }
              }
            }
        }
        return;

    }

    public static function getProductCateLists($tableId,$languageId,$number)
    {
        $model = new ProductCateMOdel();
        $cateLists =$model->whereSiteId()
            ->where('product_category.table_id',$tableId)
            ->where('language_id',$languageId)
            ->limit($number)
            ->leftjoin('product_category_description','product_category.product_cate_id', '=','product_category_description.product_cate_id')
            ->getHumpArray([
                'product_category.pid',
                'product_category.product_cate_id',
                'product_category_description.product_cate_title',
            ]);
        return $cateLists;
    }


}
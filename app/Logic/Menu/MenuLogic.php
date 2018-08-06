<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Menu;

use App\Logic\BannersLogic;
use App\Logic\Common\TreeLogic;
use App\Logic\Gallery\GalleryLogic;
use App\Logic\Site\SiteLogic;
use App\Logic\About\SidebarLogic;
use App\Logic\Works\WorksCateLogic;
use App\Logic\Works\WorksLogic;
use App\Model\Menu\MenuModel;
use App\Model\Menu\MenuDescModel;
use App\Model\Menu\MenuUrlModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class MenuLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $main=[
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'newOpen'=>$data['newOpen'],
            'sort'=>$data['sort'],
            /*'menuType'=>$data['menuType']*/
        ];
        self::addAffair($main,$data);
    }

    //添加事务
    public static function addAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['menuThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['menuThumb']);
            }
            $MenuId=self::addMenu($main);
            foreach ($data['lang'] as $key=>$value ){
                $desc=[
                    'menuId' => $MenuId,
                    'galleryId' => $GalleryId,
                    'menuType'=>$value['menuType'],
                    'menuTitle' => $value['menuTitle'],
                    'classType'=>empty($value['classType']) ? 0 : $value['classType'],
                    'classId'=>empty($value['classId']) ? 0 : $value['classId'],
                    'languageId'=>$value['languageId'],
                    'menuThumb' => empty($data['menuThumb']) ? '' : $data['menuThumb'],
                    'menuUrl' => empty($value['menuUrl']) ? '' : $value['menuUrl'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                $menuDescId = self::addMenuDesc($desc);
                $menuUrl=[
                    'menuDescId'=>$menuDescId,
                    'menuType'=>empty($value['menuPath']['menuType']) ? 1 : $value['menuPath']['menuType'],
                    'urlLink'=>empty($value['menuPath']['urlLink']) ? '' : $value['menuPath']['urlLink'],
                    'urlName'=>empty($value['menuPath']['urlName']) ? '' : $value['menuPath']['urlName'],
                    'tableId'=>empty($value['menuPath']['tableId']) ? 1 : $value['menuPath']['tableId'],
                    'cateId'=>empty($value['menuPath']['cateId']) ? '' : $value['menuPath']['cateId'],
                    'descId'=>empty($value['menuPath']['descId']) ? '' : $value['menuPath']['descId'],
                ];
                self::addMenuUrlDesc($menuUrl);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //添加主表
    public static function addMenu ($data=[])
    {
        $model = new MenuModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addMenuDesc ($data=[])
    {
        $model = new MenuDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();
    }

    //添加URL详情
    public static function addMenuUrlDesc($data=[]){
        $model = new MenuUrlModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }



    //获取分类列表
    public static function getMenuList($data=[])
    {
        $languageId=1;
        if(isset($data['languageId'])){
            $languageId=$data['languageId'];
        }
        //是否显示
        $showName=[];
        $show=[];
        if(isset($data['isOn'])){
            $showName='menu.is_on';
            $show=$data['isOn'];
        }
        $MenuLists = MenuModel::whereSiteId()
            ->where('menu_description.language_id',$languageId)
            ->where($showName,$show)
            ->orderby('menu.sort','DESC')
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.menu_id',
                'menu.pid',
                'menu.site_id',
                'menu.sort',
                'menu.is_on',
                'menu_description.menu_desc_id',
                'menu_description.menu_type',
                'menu_description.class_type',
                'menu_description.class_id',
                'menu_description.language_id',
                'menu_description.menu_title',
                'menu_description.menu_url',
                'menu_description.menu_thumb',
                'menu_description.site_title',
                'menu_description.site_keywords',
                'menu_description.site_description',

            ]);
        foreach ($MenuLists as $key=>$value){
            $menuUrl = self::getMenuUrlDesc($value['menuDescId']);
            $MenuLists[$key]['menuPath']=empty($menuUrl) ? [] : $menuUrl;
        }

        $Menu = TreeLogic::Menutree($MenuLists);

        return $Menu;
    }

    //获取菜单顶级--广告图用
    public static function getMenuParentsLists($data)
    {
        $languageId=1;
        if(isset($data['languageId'])){
            $languageId=$data['languageId'];
        }
        $MenuLists = MenuModel::whereSiteId()
            ->where('menu_description.language_id',$languageId)
            //->where('menu.is_on',1)
            ->where('menu.pid',0)
            ->orderby('menu.sort','DESC')
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.menu_id',
                'menu.pid',
                'menu.site_id',
                'menu.sort',
                'menu.is_on',
                'menu_description.menu_desc_id',
                'menu_description.language_id',
                'menu_description.menu_title',
                'menu_description.menu_url',
                'menu_description.class_type',
                'menu_description.class_id',
            ]);
        if(isset($MenuLists)){
            foreach ($MenuLists as $key=>$value){
                //获取MenuURL
                $url = self::getMenuUrl($value['menuDescId']);
                $MenuLists[$key]['tableId']=empty($url['tableId']) ? '' : $url['tableId'];
                $MenuLists[$key]['urlName']=empty($url['urlName']) ? '' : $url['urlName'];
                if($value['classType']==4){
                    //查侧栏的分组
                    $res = SidebarLogic::getSidebarByAboutId($value['classId']);
                $MenuLists[$key]['group']=empty($res['group']) ? '' : $res['group'];
                }
            }
        }
        return $MenuLists;
    }

    public static function getMenuUrl($menuDescId)
    {
        $Menu = MenuUrlModel::where('menu_desc_id', $menuDescId)->firstHumpArray(['*']);
        return $Menu;
    }

    //获取主单条
    public static function getMenuOne($menuId)
    {
        $Menu = MenuModel::where('menu_id', $menuId)
            ->firstHump(['*']);
        if(isset($Menu)){
            $MenuDesc = self::getMenuDesc($Menu['menuId']);
            if(isset($MenuDesc)){
                foreach ($MenuDesc as $key=>$value){
                    $Menu['menuThumb']=empty($value['menuThumb']) ? '' : $value['menuThumb'];
                    $menuUrlDesc = self::getMenuUrlDesc($value['menuDescId']);
                    $MenuDesc[$key]['menuPath'] = empty($menuUrlDesc) ? [] : $menuUrlDesc;
                }
                $Menu['lang']=empty($MenuDesc) ? [] : $MenuDesc;
            }
        }
        return $Menu;
    }

    public static function getMenuName($menuId,$languageId)
    {
        $langId=1;
        if(!empty($languageId)){
            $langId=$languageId;
        }
        $Menu = MenuModel::where('menu.menu_id', $menuId)
            ->where('menu_description.language_id',$langId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray(['menu_description.menu_title']);

        return $Menu;
    }


    //获取详情全部
    public static function getMenuDesc($menuId)
    {
        $MenuDesc = MenuDescModel::where('menu_id', $menuId)->getHump(['*']);
        return $MenuDesc;
    }

    //获取Url详情全部
    public static function getMenuUrlDesc($menuDescId)
    {
        $MenuUrlDesc = MenuUrlModel::where('menu_desc_id', $menuDescId)->firstHumpArray(['*']);
        return $MenuUrlDesc;
    }


    //编辑全部
    public static function editAll ($data=[])
    {
        $main=[
            'pid' => $data['pid'],
            'isOn'=>$data['isOn'],
            'newOpen'=>$data['newOpen'],
            'sort'=>$data['sort'],
            /*'menuType'=>$data['menuType']*/
        ];
        self::editAffair($main,$data);
    }

    //修改事务
    public static function editAffair($main,$data)
    {
        \DB::beginTransaction();
        try{
            $GalleryId=0;
            if(!empty($data['menuThumb'])){
                $GalleryId = GalleryLogic::getGalleryId($data['menuThumb']);
            }
            $menuId=$data['menuId'];
            self::editMenu($main,$menuId);
            foreach ($data['lang'] as $key=>$value){//多传一个详情ID过来
                $desc=[
                    'galleryId' =>$GalleryId,
                    'menuTitle' => $value['menuTitle'],
                    'menuType'=>$value['menuType'],
                    'classType'=>empty($value['classType']) ? 0 : $value['classType'],
                    'classId'=>empty($value['classId']) ? 0 : $value['classId'],
                    'languageId'=>$value['languageId'],
                    'menuThumb' => empty($data['menuThumb']) ? '' : $data['menuThumb'],
                    'menuUrl' => empty($value['menuUrl']) ? '' : $value['menuUrl'],
                    'siteTitle' => empty($value['siteTitle']) ? '' : $value['siteTitle'],
                    'siteKeywords' => empty($value['siteKeywords']) ? '' : $value['siteKeywords'],
                    'siteDescription' => empty($value['siteDescription']) ? '' : $value['siteDescription'],
                ];
                self::editMenuDesc($desc,$menuId,$value['languageId']);
                //修改菜单详细链接
                $menuUrl=[
                    'menuDescId'=>$value['menuDescId'],//多传一个详情ID过来
                    'menuType'=>$value['menuType'],
                    'urlLink'=>empty($value['menuPath']['urlLink']) ? '' : $value['menuPath']['urlLink'],
                    'urlName'=>empty($value['menuPath']['urlName']) ? '' : $value['menuPath']['urlName'],
                    'tableId'=>empty($value['menuPath']['tableId']) ? '1' : $value['menuPath']['tableId'],
                    'cateId'=>empty($value['menuPath']['cateId']) ? '' : $value['menuPath']['cateId'],
                    'descId'=>empty($value['menuPath']['descId']) ? '' : $value['menuPath']['descId'],
                ];
                self::editMenuUrlDesc($menuUrl);
            }
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }

    //编辑主表
    public static function editMenu($data=[],$menuId)
    {
        MenuModel::where('menu_id', $menuId)->updateByHump($data);
    }

    //是否显示
    public static function isShow($data=[],$menuId)
    {
        MenuModel::where('menu_id', $menuId)->updateByHump($data);
    }

    //编辑详细表
    public static function editMenuDesc($data=[],$menuId,$languageId)
    {
        MenuDescModel::where('menu_id', $menuId)->where('language_id',$languageId)->updateByHump($data);
    }

    //编辑--删除url详
    public static function editMenuUrlDesc($data)
    {
        //查看是否有URL-有就删除再添加
        $menu = MenuUrlModel::where('menu_desc_id', $data['menuDescId'])->getHumpArray(['*']);
        if(!empty($menu)){
            \DB::beginTransaction();
            try{
                self::deletMenuUrlDesc($data['menuDescId']);
                self::addMenuUrlDesc($data);
                \DB::commit();
            }catch(QueryException $e){
                \DB::rollBack();
                return false;
            }
            return true;
        }
    }

    /*//编辑详细url
    public static function editMenuUrlDesc($menuDescId,$data)
    {
        MenuUrlModel::where('menu_desc_id', $menuDescId)->updateByHump($data);
    }*/

    //删除事务
    public static function delAffair($menuId)
    {
        \DB::beginTransaction();
        try{
            self::deleteMenu($menuId);
            self::deleteMenuDesc($menuId);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return true;
    }
    //删除主
    public static function deleteMenu($menuId)
    {
        $child = self::getChildId($menuId);
        if (isset($child)){
            throw new RJsonError('该类下还有分类数据', 'DELETE_MENU');
        }
        (new MenuModel())->where('menu_id', $menuId)->delete();
    }
    //查主表单条
    public static function getMenu($menuId)
    {
        $Menu = MenuModel::where('menu_id', $menuId)
            ->firstHump(['*']);
        return $Menu;
    }
    //删除详
    public static function deleteMenuDesc($menuId)
    {
        $menu = MenuDescModel::where('menu_id', $menuId)->firstHump(['menu_desc_id']);
        if($menu){
            self::deletMenuUrlDesc($menu->menuDescId);
        }
        (new MenuDescModel())->where('menu_id', $menuId)->delete();
    }
    //删除详细URL
    public static function deletMenuUrlDesc($menuDescId)
    {
        (new MenuUrlModel())->where('menu_desc_id', $menuDescId)->delete();
    }

    //获取上一级ID
    public static function getMenuId($menuId)
    {
        $Menu = MenuModel::where('menu.menu_id', $menuId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHump([
                'menu.pid'
            ]);
        return $Menu;

    }

    //获取下一级
    public static function getChildId($menuId)
    {
        $Menu = MenuModel::where('menu.pid', $menuId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHump([
                'menu.menu_id'
            ]);
        return $Menu;

    }

    //获取某个菜单下的所有子类
    public static function getMenuKids($menuId,$languageId)
    {
        $Ids=self::getCateId($menuId);
        $MenuLists = MenuModel::whereIn('menu.menu_id',$Ids)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.*',
                'menu_description.*'
            ]);
        $Menu = TreeLogic::Menutree($MenuLists);
        return $Menu;
    }

    //获取类下的所有子类ID
    public static function getCateId($menuId)
    {
        $model = new MenuModel();
        $data = $model->whereSiteId()->where('pid',$menuId)->getHumpArray(['pid','menu_id']);
        $arr=[];
        if(isset($data)){
            foreach ($data as $value) {
                $arr[]=$value['menuId'];
            }
        }
        $arr2 = self::menuTree($arr);
        //保留一个相同值
        $arr3 = array_unique($arr2);
        //只显示值，不显示键值
        $arr4 = array_values($arr3);
        $res = array_merge($arr,$arr4);
        $menuId=(int) $menuId;
        array_push($res,$menuId);
        return $res;
    }
    public static function menuTree($menuId)
    {
        $model = new MenuModel();
        $arr = array();
        $arr3=[];
        $data = $model->whereSiteId()->whereIn('pid',$menuId)->getHumpArray(['pid','menu_id']);
        if(isset($data)){
            foreach ($data as $v) {
                $arr[] = $v['menuId'];
                $arr2 =self::menuTree($arr);
                //合并多个数组
                $arr3=array_merge($arr,$arr2);
            }
        }
        return $arr3;
    }





    //=========================前端调用单条==============================

    //获取主单条
    public static function getMenuOnes($menuId,$languageId)
    {
        $Menu = MenuModel::where('menu_description.menu_id', $menuId)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $menuUrlDesc = self::getMenuUrlDesc($Menu['menuDescId']);
        $Menu['menuPath']=empty($menuUrlDesc) ? '' : $menuUrlDesc;
        return $Menu;
    }

    public static function getMenusParents($menuId,$languageId)
    {
        $menu = MenuModel::whereSiteId()
            //->where('menu.pid', $menuId)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $arr=TreeLogic::getParents($menu,$menuId);
        foreach ($arr as $key=>$value){
            $menuUrlDesc = self::getMenuUrlDesc($value['menuDescId']);
            $arr[$key]['menuPath'] = empty($menuUrlDesc) ? [] : $menuUrlDesc;
        }
        return $arr;
    }

    //获取--面包屑
    public static function getMenuNameByClassId($classId,$languageId,$classType)
    {
        $arr=[];
        $Menu = MenuModel::where('menu_description.class_id', $classId)
            ->where('menu_description.class_type',$classType)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        if($Menu['pid']!=0){
            $res = self::getMenusParents($Menu['menuId'],$languageId);
            $arr=empty($res) ? '' : $res;
        }else{
            $menuUrlDesc = self::getMenuUrlDesc($Menu['menuDescId']);
            if(!empty($Menu)){
                $Menu['menuPath'] = empty($menuUrlDesc) ? [] : $menuUrlDesc;
                $arr[]=$Menu;
            }
        }
        return $arr;
    }
    //检查--面包屑
    public static function getMenuCheckByClassId($classId,$languageId,$classType)
    {
        $Menu = MenuModel::where('menu_description.class_id', $classId)
            ->where('menu_description.class_type',$classType)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $count=count($Menu);
        return $count;
    }
    //获取导航名称--面包屑
    public static function getMenuParents($menuId,$languageId)
    {
        $Menu = MenuModel::whereSiteId()
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $res = TreeLogic::getMenuParents($Menu,$menuId);
        return $res;
    }
    //获取导航名称--面包屑
    public static function getMenuByClassId($classId,$languageId,$classType)
    {
        $arr=[];
        $Menu = MenuModel::where('menu_description.class_id', $classId)
            ->where('menu_description.class_type',$classType)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        if($Menu['pid']!=0){
            $res = self::getMenuByMenuId($Menu['pid'],$languageId);
            $arr=empty($res) ? '' : $res;
        }else{
            $menuUrlDesc = self::getMenuUrlDesc($Menu['menuDescId']);
            if(!empty($Menu)){
                $Menu['menuPath'] = empty($menuUrlDesc) ? [] : $menuUrlDesc;
                $arr[]=$Menu;
            }
        }
        return $arr;
    }
    public static function getMenuByMenuId($menuId,$languageId)
    {
        $arr=[];
        $Menu = MenuModel::where('menu.menu_id', $menuId)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        if($Menu['pid']!=0){
            self::getMenuByMenuId($Menu['pid'],$languageId);
        }else{
            $menuUrlDesc = self::getMenuUrlDesc($Menu['menuDescId']);
            if(!empty($Menu)){
                $Menu['menuPath'] = empty($menuUrlDesc) ? [] : $menuUrlDesc;
                $arr[]=$Menu;
            }
        }
        return $arr;
    }

    //获取banner
    public static function getMenuBannerByClassId($classId,$languageId,$classType)
    {
        $Menu = MenuModel::where('menu_description.class_id', $classId)
            ->where('menu_description.class_type',$classType)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);

        return $Menu;
    }
    public static function getMenuBanner($menuId,$languageId)
    {
        $langId=1;
        if(!empty($languageId)){
            $langId=$languageId;
        }
        $Menu = MenuModel::whereSiteId()
            ->where('menu_description.language_id',$langId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->getHumpArray(['menu.*','menu_description.*']);
        $arr=TreeLogic::getParents($Menu,$menuId);
        return $arr;
    }
    //根据URL获取menuId
    public static function getMenuIdByUrl($urlName,$tableId,$languageId)
    {
        $arr=[];
        $Menu = MenuModel::whereSiteId()
            ->where('menu_url.url_name', $urlName)
            ->where('menu_url.table_id',$tableId)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->leftjoin('menu_url', 'menu_description.menu_desc_id', '=', 'menu_url.menu_desc_id')
            ->getHumpArray([
                'menu.*',
                'menu_description.*',
                'menu_url.*',
            ]);
        foreach ($Menu as $value){
            $res = parse_url($value['urlLink']);
            if(empty($res['query'])){
                $arr=$value;
            }
        }
        return $arr;
    }
    public static function getMenuNameByUrl($urlName,$tableId,$languageId)
    {
        $arr=[];
        $Menu = MenuModel::whereSiteId()
            ->where('menu.pid',0)
            ->where('menu_url.url_name', $urlName)
            ->where('menu_url.table_id',$tableId)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->leftjoin('menu_url', 'menu_description.menu_desc_id', '=', 'menu_url.menu_desc_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
                'menu_url.*',
            ]);

        return $Menu;
    }
    public static function getMenuNameByWorks($url,$tableId,$languageId)
    {
        $newUrl=[];
        $worksArr=[];
        $cateId=[];
        $path = explode('?',$url);
        $url1=$path[0];
        if(!empty($path[1])){
            $res = explode('=',$path[1]);
            $cateId=$res[1];
        }
        $url3 = explode('/',$url1);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        //unset($url3[count($url3) -1]);
        $url4 = implode('/',$url3);
        $newUrl=$url4.'/lists/works/1';
        $path1 = parse_url($url1);
        $path2 = explode('/',$path1['path']);
        if($path2[3]!=1){
            $worksId=$path2[3];
            //获取作品
            $res = WorksLogic::getWorks($worksId,$languageId);
            $cateId=$res['worksCateId'];
            unset($res['worksCateTitle']);
            $worksArr[]=$res;
        }
        $Menu = MenuModel::whereSiteId()
            ->where('menu_description.menu_url', $newUrl)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $menuArray = self::getMenusParents($Menu['menuId'],$languageId);
        $worksCate=[];
        if(!empty($cateId)){
            //获取分类
            $worksCate = WorksCateLogic::getCateName($cateId,$languageId);
        }
        //var_dump($menuArray);
        //var_dump($worksCate);
        $menuName = array_merge($menuArray,$worksCate,$worksArr);
        return $menuName;
    }

    public static function getWorksMenuBanner($url,$tableId,$languageId)
    {
        /*$arr = parse_url($url);
        $path = explode('/',$arr['path']);
        //lists---分类
        if($path[2]!='lists'){
            $urlName=array($path[0],$path[1],'lists',$path[3],'1');
            $newUrl = implode('/',$urlName);
        }else{
            $newUrl=$arr['path'];
        }*/
        $path = explode('?',$url);
        $url1=$path[0];
        $url3 = explode('/',$url1);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        $url4 = implode('/',$url3);
        $newUrl=$url4.'/lists/works/1';
        $Menu = MenuModel::whereSiteId()
            ->where('menu_description.menu_url', $newUrl)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $menuArray = self::getMenusParents($Menu['menuId'],$languageId);
        return $menuArray;
    }

    public static function getTeacherMenuBanner($url,$tableId,$languageId)
    {
        /*$arr = parse_url($url);
        $path = explode('/',$arr['path']);
        //lists---分类
        if($path[2]!='lists'){
            $urlName=array($path[0],$path[1],'lists',$path[3],'1');
            $newUrl = implode('/',$urlName);
        }else{
            $newUrl=$arr['path'];
        }*/
        $path = explode('?',$url);
        $url1=$path[0];
        $url3 = explode('/',$url1);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        unset($url3[count($url3) -1]);
        $url4 = implode('/',$url3);
        $newUrl=$url4.'/lists/teacher/1';
        $Menu = MenuModel::whereSiteId()
            ->where('menu_description.menu_url', $newUrl)
            ->where('menu_description.language_id',$languageId)
            ->leftjoin('menu_description', 'menu.menu_id', '=', 'menu_description.menu_id')
            ->firstHumpArray([
                'menu.*',
                'menu_description.*',
            ]);
        $menuArray = self::getMenusParents($Menu['menuId'],$languageId);
        return $menuArray;
    }


}
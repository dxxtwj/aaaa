<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Common;
use App\Http\Middleware\SiteId;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class TreeLogic
{
    /**
     * 主键名称
     * @var string
     */
    private static $primary = 'id';
    /**
     * 父键名称
     * @var string
     */
    private static $parentId = 'parent_id';
    /**
     * 子节点名称
     * @var string
     */
    private static $child    = 'child';
    /**
     * 修改主键名称、父键名称、子节点名称
     * @param string $primary
     * @param string $parentId
     * @param string $child
     */
    public static function setConfig($primary = '', $parentId = '', $child = ''){
        if(!empty($primary))  self::$primary  = $primary;
        if(!empty($parentId)) self::$parentId = $parentId;
        if(!empty($child))    self::$child    = $child;
    }
    /**
     * 生成Tree
     * @param array $data
     * @param number $index
     * @return array
     */
    public static  function  makeTree(&$data, $index = 0)
    {
        $childs = self::findChild($data, $index);
        if(empty($childs))
        {
            return $childs;
        }
        foreach($childs as $k => &$v)
        {
            if(empty($data)) break;
            $child = self::makeTree($data, $v[self::$primary]);
            if(!empty($child))
            {
                $v[self::$child] = $child;
            }
        }
        unset($v);
        return $childs;
    }
    /**
     * 查找子类
     * @param array $data
     * @param number $index
     * @return array
     */
    public static function findChild(&$data, $index)
    {
        $childs = [];
        foreach ($data as $k => $v){
            if($v[self::$parentId] == $index){
                $childs[]  = $v;
                unset($v);
            }
        }
        return $childs;
    }

    //新闻
    public static function Newstree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::Newstree($cate, $name, $v['newsCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
    //获取某个分类的所有子分类
    public static function getSubs($cate,$catId=0, $name = 'child',$level=1){
        $subs=array();
        foreach($cate as $item){
            if($item['pid']==$catId){
                $item[$name]=self::getSubs($cate,$item['newsCateId'],$name,$level+1);
                $subs[]=$item;
            }

        }
        return $subs;
    }

    //案例
    public static function Casestree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::Casestree($cate, $name, $v['casesCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
    //获取某个分类的所有子分类
    public static function CasesSubs($cate,$catId=0, $name = 'child',$level=1){
        $subs=array();
        foreach($cate as $item){
            if($item['pid']==$catId){
                $item[$name]=self::CasesSubs($cate,$item['casesCateId'],$name,$level+1);
                $subs[]=$item;
            }

        }
        return $subs;
    }

    //关于我们
    public static function Abouttree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::Abouttree($cate, $name, $v['aboutCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    //产品
    public static function Producttree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::Producttree($cate, $name, $v['productCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
    //获取某个分类的所有子分类
    public static function ProSubs($cate,$catId=0, $name = 'child',$level=1){
        $subs=array();
        foreach($cate as $item){
            if($item['pid']==$catId){
                $item[$name]=self::ProSubs($cate,$item['productCateId'],$name,$level+1);
                $subs[]=$item;
            }

        }
        return $subs;
    }

    //菜单
    public static function Menutree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $key=>$v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::Menutree($cate, $name, $v['menuId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    //菜单地址
    public static function MenuUrl($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::MenuUrl($cate, $name, $v['cateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    //传递一个子分类ID返回所有的父级分类
    Public static function getParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['menuId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }

    //传递一个子分类ID返回所有的父级分类
    Public static function getNewsParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['newsCateId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getNewsParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }

    //传递一个子分类ID返回所有的父级分类
    Public static function getProductParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['productCateId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getProductParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }

    //传递一个子分类ID返回所有的父级分类
    Public static function getCasesParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['casesCateId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getCasesParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }

    //传递一个子分类ID返回所有的父级分类
    Public static function getMenuParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['menuId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getMenuParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }

    //作品分类
    public static function WorksTree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::WorksTree($cate, $name, $v['worksCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }
    //传递一个子分类ID返回所有的父级分类
    Public static function getWorksParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['worksCateId'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getWorksParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }


}
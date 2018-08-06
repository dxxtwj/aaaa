<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10\Common;
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
    public static function starTree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::starTree($cate, $name, $v['starCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    //案例
    public static function casesTree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::casesTree($cate, $name, $v['casesCateId']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    //关于我们
    public static function newsTree($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v[$name] =self::newsTree($cate, $name, $v['newsCateId']);
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

    //传递一个子分类ID返回所有的父级分类
    Public static function getParents ($cate, $id) {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['id'] == $id) {
                $arr[] = $v;
                $arr = array_merge(self::getParents($cate, $v['pid']), $arr);
            }
        }
        return $arr;
    }


}
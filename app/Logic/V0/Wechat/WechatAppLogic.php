<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Wechat;

use App\Model\V0\Wechat\AppletAuditidModel;
use App\Model\V0\Wechat\WechatAppModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class WechatAppLogic
{
    /*
     * 添加
     * */
    public static function add($data)
    {
        $model = new WechatAppModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    /*
     * 获取列表
     * */
    public static function getList($data)
    {
        $model = new WechatAppModel();
        if(isset($data['wechatAppId'])){
            $model = $model->where('wechat_app_id',$data['wechatAppId']);
        }
        if(isset($data['isRelease'])){
            $model = $model->where('is_release',$data['isRelease']);
        }
        if(isset($data['appName'])){
            $model = $model->where('app_name','like','%'.$data['appName'].'%');
        }
        $lists = $model->getDdvPageHumpArray(true);
        return $lists;
    }
    /*
     * 查单条
     * */
    public static function getOne($appletsId)
    {
        $model = new WechatAppModel();
        $res = $model->where('applets_id', $appletsId)->firstHumpArray(['*']);
        return $res;
    }
    /*
     * 修改
     * */
    public static function edit($data)
    {
        $model = new WechatAppModel();
        $model->where('applets_id', $data['appletsId'])->updateByHump($data);
        return;
    }
    /*
     * 删除
     * */
    public static function delete($appletsId)
    {
        $model = new WechatAppModel();
        $model->where('applets_id',$appletsId)->delete();
    }

    /**
     * 添加版本
     * */
    public static function addAuditid($wechatAppId,$template,$Auditid)
    {
        $data=[
            'wechatAppId'=>$wechatAppId,
            'auditid'=>$Auditid,
            'templateId'=>$template['templateId'],
            'version'=>$template['version'],
            'desc'=>$template['desc'],
        ];
        $model = new AppletAuditidModel();
        $model->setDataByHumpArray($data)->save();
        return;
    }
    /**
     * 获取版本列表
     * */
    public static function getAuditidLists($wechatAppId)
    {
        $model = new AppletAuditidModel();
        $model = $model->where('wechat_app_id',$wechatAppId);
        $lists = $model->getDdvPageHumpArray(true);
        return $lists;
    }


}
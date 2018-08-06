<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Model\Admin\AdminModel;
use App\Model\Admin\AdminDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class AdminLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $mian=[
            'siteId'=> $data['siteId']
        ];
        $adminId=self::addAdmin($mian);
        $password=md5($data['adminPassword']);
        $desc=[
            'adminId' => $adminId,
            'adminName' => $data['adminName'],
            'adminPassword' => $password,
        ];
        self::addAdminDesc($desc);
    }

    //添加主表
    public static function addAdmin ($data=[])
    {
        $model = new AdminModel();
        $model->setDataByHumpArray($data)->save();
        return $model->getQueueableId();

    }

    //添加详细表
    public static function addAdminDesc ($data=[])
    {
        $model = new AdminDescModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取列表
    public static function getAdminList($data)
    {
        $name=[];
        $where=[];
        if(isset($data['siteId'])){
            $name='admin.site_id';
            $where=$data['siteId'];
        }
        $AdminLists = AdminModel::where($name,$where)
            ->orderby('admin.site_id','ASC')
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->leftjoin('site', 'admin.site_id', '=', 'site.site_id')
            ->getHumpArray([
                'admin.*',
                'admin_description.*',
                'site.site_name',
            ]);
        return $AdminLists;
    }

    //获取住单条
    public static function getAdminOne($adminId)
    {
        $Admin = AdminModel::where('admin.admin_id', $adminId)
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->leftjoin('site', 'admin.site_id', '=', 'site.site_id')
            ->firstHump(['admin.*','admin_description.*','site.site_name']);
        return $Admin;
    }

    //获取住单条
    public static function getAdminLogin($adminId)
    {
        $Admin = AdminModel::where('admin.admin_id', $adminId)
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->firstHump([
                'admin.*',
                'admin_description.admin_name',
                'admin_description.admin_password',
            ]);
        return $Admin;
    }


    //编辑全部
    public static function editPassword ($data=[])
    {
        $adminId = \Session::get('adminId');
        $adminInfo=self::getAdminLogin($adminId);
        if($adminInfo->adminPassword != md5($data['passwordOld'])){
            throw new RJsonError('原密码错误', 'PASSWORD_ERROR');
        }
        if($data['passwordNew']!=$data['passwordCon']){
            throw new RJsonError('确认密码错误', 'PASSWORD_CON_ERROR');
        }
        $passwordNew['adminPassword']=md5($data['passwordNew']);
        self::editAdminDesc($passwordNew,$adminInfo->adminId);
    }

    //重置密码
    public static function ResetPassword($data)
    {
        $passwordNew['adminPassword']=md5($data['passwordNew']);
        self::editAdminDesc($passwordNew,$data['adminId']);
    }


    //编辑主表
    public static function editAdmin($data=[],$adminId)
    {
        AdminModel::where('admin_id', $adminId)->updateByHump($data);

    }
    //编辑详细表----有多语言的修改
    public static function editAdminDesc($data=[],$adminId)
    {
        AdminDescModel::where('admin_id', $adminId)->updateByHump($data);
    }

    //删除主
    public static function deleteAdmin($AdminId)
    {
        (new AdminModel())->where('admin_id', $AdminId)->delete();
        self::deleteAdminDesc($AdminId);
    }
    //删除详
    public static function deleteAdminDesc($AdminId)
    {
        (new AdminDescModel())->where('admin_id', $AdminId)->delete();
    }

    //=========================登录用==============================

    //查单条
    public static function getAdmin($adminName)
    {
        $Admin = AdminModel::whereSiteId()
            ->where('admin_description.admin_name','=',$adminName)
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->firstHump([
                'admin.*',
                'admin_description.*',
            ]);

        return $Admin;
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Admin;

use App\Logic\V0\Site\SiteLogic;
use App\Model\V0\Admin\AdminDescModel;
use App\Model\V0\Admin\AdminModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class AdminLogic
{
    //全部
    public static function addAll ($data=[])
    {
        $mian=[
            'siteId'=> $data['siteId']
        ];
        $name=SiteLogic::getSiteName($data['siteId']);
        $adminId=self::addAdmin($mian);
        $password=md5($data['adminPassword']);
        $desc=[
            'adminId' => $adminId,
            'adminName' => $data['adminName'],
            'siteName'=>$name,
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

    public static function addAdminDefault($siteId,$siteName)
    {
        \DB::beginTransaction();
        try{
            $main=[
                'siteId'=>$siteId,
            ];
            $adminId = self::addAdmin($main);
            $desc=[
                'adminName'=>'admin',//名称
                'adminPassword'=>'14e1b600b1fd579f47433b88e8d85291',//123456
                'adminId'=>$adminId,
                'siteName'=>$siteName,//站点名称
            ];
            self::addAdminDesc($desc);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            throw new RJsonError($e->getMessage(), $e->getCode());
        }
        return;
    }

    //获取列表
    public static function getAdminList($data)
    {
        $model = new AdminModel();
        if(isset($data['siteId'])){
            $model = $model->where('admin.site_id',$data['siteId']);
        }
        $model->orderBy('admin.site_id','ASC');
        $AdminLists = $model->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->select([
                'admin.*',
                'admin_description.*',
            ]);
        return $AdminLists->getDdvPageHumpArray(true);
    }

    //获取住单条
    public static function getAdminOne($adminId)
    {
        $Admin = (new AdminModel())->where('admin.admin_id', $adminId)
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->firstHump(['admin.*','admin_description.admin_name','admin_description.site_name']);
        return $Admin;
    }

    //获取住单条
    public static function getAdminLogin()
    {
        //$adminId = session('admin_id');
        $adminId=12;
        $Admin = (new AdminModel())->where('admin.admin_id', $adminId)
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
        $adminInfo=self::getAdminLogin();
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
        (new AdminModel())->where('admin_id', $adminId)->updateByHump($data);

    }
    //编辑详细表----有多语言的修改
    public static function editAdminDesc($data=[],$adminId)
    {
        (new AdminDescModel())->where('admin_id', $adminId)->updateByHump($data);
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
        $Admin = (new AdminModel())->whereSiteId()
            ->where('admin_description.admin_name','=',$adminName)
            ->leftjoin('admin_description', 'admin.admin_id', '=', 'admin_description.admin_id')
            ->firstHump([
                'admin.*',
                'admin_description.*',
            ]);

        return $Admin;
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Model\V10\AdminModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class AdminLogic
{

    //添加主表
    public static function addAdmin($data=[])
    {
        \DB::beginTransaction();
        try{
            $data['password']=md5($data['password']);
            $model = new AdminModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

    //编辑
    public static function editPassword ($data)
    {
        $adminId = LoginLogic::getLoginId();
        $AdminInfo=self::getAdminById($adminId);
        if($AdminInfo['password'] != md5($data['passwordOld'])){
            throw new RJsonError('原密码错误', 'PASSWORD_ERROR');
        }
        if($data['passwordNew']!=$data['passwordCon']){
            throw new RJsonError('确认密码错误', 'PASSWORD_CON_ERROR');
        }
        $passwordNew['password']=md5($data['passwordNew']);
        self::editAdmin($passwordNew,$adminId);
    }
    //修改主表
    public static function editAdmin($passwordNew,$adminId)
    {
        \DB::beginTransaction();
        try{
            $model = new AdminModel();
            $model->where('admin_id', $adminId)->updateByHump($passwordNew);
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }
    public static function getAdmin($name)
    {
        $model = new AdminModel();
        $Admin = $model->where('name',$name)->firstHumpArray(['*']);
        return $Admin;
    }

    public static function getAdminById($adminId)
    {
        $model = new AdminModel();
        $Admin = $model->where('admin_id',$adminId)->firstHumpArray(['*']);
        return $Admin;
    }


}
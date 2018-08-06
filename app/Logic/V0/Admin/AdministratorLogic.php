<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\Admin;


use App\Model\V0\Admin\AdministratorModel;
use App\Model\V0\Site\SiteModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class AdministratorLogic
{

    //添加
    public static function addAdministrator ($data=[])
    {
        $password=md5($data['administratorPassword']);
        $desc=[
            'administratorName' => $data['administratorName'],
            'administratorPassword' => $password,
        ];
        $model = new AdministratorModel();
        $model->setDataByHumpArray($desc)->save();
        return $model;

    }

    //获取列表
    public static function getAdministratorList()
    {
        $AdministratorLists = (new AdministratorModel())->select(['*']);
        return $AdministratorLists->getDdvPageHumpArray(true);
    }

    //获取住单条
    public static function getAdministratorOne($administratorId)
    {
        $Administrator = (new AdministratorModel())->where('administrator_id', $administratorId)->firstHump(['*']);
        return $Administrator;
    }

    //编辑
    public static function editPassword ($data)
    {
        $administratorId = OurLoginLogic::getLoginId();
        $AdministratorInfo=self::getAdministratorOne($administratorId);
        if($AdministratorInfo->administratorPassword != md5($data['passwordOld'])){
            throw new RJsonError('原密码错误', 'PASSWORD_ERROR');
        }
        if($data['passwordNew']!=$data['passwordCon']){
            throw new RJsonError('确认密码错误', 'PASSWORD_CON_ERROR');
        }
        $passwordNew['administratorPassword']=md5($data['passwordNew']);
        self::editAdministrator($passwordNew,$administratorId);
    }


    //获取单条--登录用
    public static function getAdministrator($administratorName)
    {

        $Administrator = (new AdministratorModel())->where('administrator_name',$administratorName)
            ->firstHump(['*']);
        return $Administrator;
    }

    //获取单条--登录后获取信息
    public static function getAdministratorLogin()
    {
        $administratorId = OurLoginLogic::getLoginId();
        $Administrator = (new AdministratorModel())->where('administrator_id',$administratorId)
            ->firstHump(['*']);
        return $Administrator;
    }


    //重置密码
    public static function ResetPassword($data)
    {
        $passwordNew['administratorPassword']=md5($data['administratorPassword']);
        $passwordNew['administratorName']=$data['administratorName'];
        self::editAdministrator($passwordNew,$data['administratorId']);
    }

    //编辑
    public static function editAdministrator($data=[],$administratorId)
    {
        (new AdministratorModel())->where('administrator_id', $administratorId)->updateByHump($data);

    }

    //删除主
    public static function deleteAdministrator($administratorId)
    {
        (new AdministratorModel())->where('administrator_id', $administratorId)->delete();

    }

    //链接数据库测试
    public static function getSiteList()
    {
        $AdministratorLists = (new SiteModel())->select(['*']);
        return $AdministratorLists->getDdvPageHumpArray(true);
    }


}
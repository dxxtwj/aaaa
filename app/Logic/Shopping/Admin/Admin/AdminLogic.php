<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\Admin;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shopping\Admin\AdminModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;

use Illuminate\Database\QueryException;

class AdminLogic extends ShoppingLogic
{
    /*
     * 后台管理员登录  未完成，  问题是session存储不进去
     */
    public static function login($data) {

        $adminModel = new AdminModel();

        $res = $adminModel->where('admin_name', $data['adminName'])->firstHumpArray();

        $adminPassword = password_verify($data['adminPassword'], $res['adminPassword']);//哈希解密

        if ((string)$res['adminName'] != (string)$data['adminName'] || (bool)$adminPassword === false) {

            throw new RJsonError('账号或密码错误', 'ADMIN_ERROR');
        }

        $adminLoginData = array('adminId' => $res['adminId'], 'adminName' => $res['adminName'], 'adminPhone' => $res['adminPhone']);

//        \Session::put('adminId', $res['adminId']);//存详细数据
        $a = \Session::get('adminId');//存管理员ID进session

        var_dump($a);exit;

    }


    /*
     * 创建管理员账号
     * @param array $data 要添加的数据
     * @param whereShopping() 判断方法
     * @param password_hash PHP7的新加密方式
     * @retrun null
     */
    public static function addAdmin($data) {

        $adminModel = new AdminModel();
        $bool = $adminModel->where('admin_name',$data['adminName'])->firstHumpArray();

        if (!empty($bool)) {

            throw new RJsonError('已经存在相同的登录账户', 'ADMIN_ERROR');
        }

        $data['adminPassword'] = password_hash($data['adminPassword'], PASSWORD_DEFAULT);//哈希加密

        $bool = $adminModel->setDataByHumpArray($data)->save();
        $lastId = $adminModel->getQueueableId();
        if (!$bool) {

            throw new RJsonError('添加管理员账号失败', 'ADMIN_ERROR');
        }

        return ;

    }

}
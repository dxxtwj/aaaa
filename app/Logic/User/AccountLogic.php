<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\User;
use \App\Model\UserAccountModel;
use \App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class AccountLogic
{
    public static function getOneByPhone ($phone = '', $columns = ['*']){
        $res = (new UserAccountModel())->where('account', $phone)->where('type', 2)->firstHump($columns);
        if (empty($res)){
            throw new RJsonError('手机号码还没有注册', 'NOT_FIND_ACCOUNT');
        }
        return $res;
    }
    public static function getOneByAccount ($account = '', $columns = ['*']){
        $res = (new UserAccountModel())->where('account', $account)->firstHump($columns);
        if (empty($res)){
            throw new RJsonError('账号不存在', 'NOT_FIND_ACCOUNT');
        }
        return $res;
    }
    public static function getOkListsByUid ($uid = '', $columns = ['*']){
        $res = (new UserAccountModel())->where('uid', $uid)->where('state','>',0)->getHump($columns);
        if (empty($res)){
            throw new RJsonError('没有找到该账户', 'NOT_FIND_ACCOUNT');
        }
        return $res;
    }
    public static function deleteOneByAccount ($account = '')
    {
        (new UserAccountModel())->where('account', $account)->delete();
    }
    public static function deleteOneByUidAndType ($uid, $type)
    {
        (new UserAccountModel())->where('uid', $uid)->where('type', $type)->delete();
    }
    public static function addOne ($data=[])
    {
        $model = new UserAccountModel();
        $model->setDataByHumpArray($data)->save();
        return $model;
    }
}

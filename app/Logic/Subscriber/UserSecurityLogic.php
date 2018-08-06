<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Subscriber;

use App\Model\Subscriber\UserModel;
use App\Model\Subscriber\UserDescModel;
use App\Model\Subscriber\UserSecurityModel;
use App\Model\Subscriber\UserSecurityAnswerModel;
use App\User;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class UserSecurityLogic
{
    //主表
    public static function add($data=[])
    {
        foreach ($data['lang'] as $value){
            self::addSecurity($value);
        }
    }
    public static function addSecurity($data=[])
    {
        $model = new UserSecurityModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model;
    }

    //获取问题
    public static function getSecurity($data){
        $name=[];
        $where=[];
        if(isset($data['languageId'])){
            $name='user_security.language_id';
            $where=$data['languageId'];
        }
        $SecurityLists = UserSecurityModel::whereSiteId()
            ->where($name,$where)
            ->orderby('user_security.sort','DESC')
            ->getHumpArray(['*']);
        return $SecurityLists;
    }

}
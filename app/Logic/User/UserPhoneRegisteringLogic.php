<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\User;
use App\Model\UserPhoneRegisteringModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserPhoneRegisteringLogic
{
    /**
     * 登录逻辑
     */
    public static function addPhone($phone=''){
        $res = UserPhoneRegisteringModel::where('phone', $phone)->first(['id']);
        if ($res){
            UserPhoneRegisteringModel::where('id', $res->id)->update([]);
        }else{
            (new UserPhoneRegisteringModel())->setDataByHumpArray([
                'phone'=>$phone
            ])->save();
        }
    }

}

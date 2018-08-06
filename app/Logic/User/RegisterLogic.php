<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\User;
use App\Model\UserBaseModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;


class RegisterLogic
{
    const SPECIAL_REGEXP = "/[`~!@#$^&*()=|{}':;',[\].<>\/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？\"+ ]/";
    public static function isUserName($str=''){
        if (is_numeric($str[0])){
            return false;
        }
        return !preg_match(self::SPECIAL_REGEXP, $str);
    }
    public static function phoneAndUsernameReg($phone, $password, $ip, $username = ''){
        // 创建
        $model = new UserBaseModel();
        $model->setDataByHumpArray([
            'registerIp' => $ip,
            'password' => $password
        ])->save();
        // 获取uid
        $uid = $model->getQueueableId();
        // 添加phone手机账号
        AccountLogic::addOne([
            'uid'=>$uid,
            'account'=>$phone,
            'registerIp' => $ip,
            'state'=>1,
            'type'=>2
        ]);
        // 如果有用户名账号就直接添加用户名账号
        if (!empty($username)){
            if (!self::isUserName($username)){
                throw new RJsonError('用户名格式错误', 'USERNAME_ERROR');
            }

            AccountLogic::addOne([
                'uid'=>$uid,
                'account'=>$username,
                'registerIp' => $ip,
                'state'=>1,
                'type'=>1
            ]);
        }

        return [
            'uid'=>$uid
        ];
    }
}
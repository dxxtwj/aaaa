<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\User;
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
    public static function Register()
    {

    }
}
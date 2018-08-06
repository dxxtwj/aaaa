<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Model\OurMessage\MessageCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class MessageCateLogic
{


    //获取列表
    public static function getMessageCateLists()
    {
        $messageLists = MessageCateModel::getHumpArray(['*']);
        return $messageLists;
    }


}
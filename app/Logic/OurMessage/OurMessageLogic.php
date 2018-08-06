<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic;

use App\Model\OurMessage\OurmessageModel;
use App\Model\OurMessage\MessageDescModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class OurMessageLogic
{

    //添加主表
    public static function addOurMessage ($data=[])
    {
        $model = new OurmessageModel();
        $model->setSiteId()->setDataByHumpArray($data)->save();
        return $model;

    }
    //获取列表
    public function getMessageCateLists()
    {
        $res = MessageCateLogic::getMessageCateLists();
        return ['lists'=>$res];
    }


}
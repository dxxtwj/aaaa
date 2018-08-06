<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V0\OurMessage;

use App\Model\V0\OurMessage\MessageCateModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use function var_dump;

class MessageCateLogic
{
    //添加主表
    public static function addMessageCate ($data=[])
    {
        $model = new MessageCateModel();
        $model->setDataByHumpArray($data)->save();
        return $model;

    }

    //获取列表
    public static function getMessageCateLists()
    {
        $model = new MessageCateModel();
        $messageLists = $model->getHumpArray(['*']);
        return $messageLists;
    }

    //查单条
    public static function getMessageCate($MessageCateId)
    {
        $model = new MessageCateModel();
        $message = $model->where('message_cate_id', $MessageCateId)->firstHump(['*']);
        return $message;
    }

    //编辑
    public static function editMessageCate ($data,$messageCateId)
    {
        $model = new MessageCateModel();
        $model->where('message_cate_id', $messageCateId)->updateByHump($data);
    }

    //删除主
    public static function deleteMessageCate($MessageCateId)
    {
        (new MessageCateModel())->where('message_cate_id', $MessageCateId)->delete();
    }




}
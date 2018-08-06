<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\V10;

use App\Logic\V10\Common\VerifyLogic;
use App\Model\V10\MessageModel;
use DdvPhp\DdvPage;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;
use function var_dump;

class MessageLogic
{

    //添加主表
    public static function add($data=[])
    {
        if(isset($data['phone'])){
            VerifyLogic::verifyPhone($data['phone']);
        }
        if(isset($data['email'])){
            VerifyLogic::verifyEmail($data['email']);
        }
        if(empty($data['content'])){
            $data['content']='';
        }
        \DB::beginTransaction();
        try{
            $model = new MessageModel();
            $model->setDataByHumpArray($data)->save();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return $model;
    }

    public static function Lists($data=[])
    {
        $model = new MessageModel();
        $lists = $model->orderBy('created_at','DESC')->getDdvPageHumpArray();
        return $lists;
    }

    public static function getOne($messageId)
    {
        $model = new MessageModel();
        $message = $model->where('message_id',$messageId)->firstHumpArray(['*']);
        return $message;
    }

    public static function delete($messageId)
    {
        \DB::beginTransaction();
        try{
            $model = new MessageModel();
            $model->where('message_id',$messageId)->delete();
            \DB::commit();
        }catch(QueryException $e){
            \DB::rollBack();
            return false;
        }
        return;
    }

}
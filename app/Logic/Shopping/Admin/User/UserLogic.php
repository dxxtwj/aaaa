<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Admin\User;

use App\Logic\Common\ShoppingLogic;
use App\Logic\Shopping\Api\Collection\CollectionLogic;
use App\Model\Shopping\User\UserModel;
use App\Model\Shopping\UserMessage\UserMessageModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserLogic extends ShoppingLogic
{

    public static function showUser($data=array()) {

        $userModel = new UserModel();
        $collection = new CollectionLogic();
        if (empty($data['userId'])) {

            $res = $userModel->getDdvPageHumpArray();

            foreach ($res['lists'] as $k => $v) {
                $res['lists'][$k]['collection'] = $collection->showCollection($res, $v['userId']);
            }

            return $res;

        } elseif(!empty($data['userId'])) {
            $res = $userModel->where('user_id',$data['userId'])->firstHumpArray();
            $res['collection'] = $collection->showCollection($res, $res['userId']);

            return ['data' => $res];

        }
    }

    // 后台查询留言反馈
    public static function showUserMessage(){

        $userModel = new UserModel();
        $userMessageModel = new UserMessageModel();
        $res = $userMessageModel->orderBy('shopping_user_message.created_at','DESC')->getDdvPageHumpArray();

        foreach ($res['lists'] as $k => $v){
            $ids[] = $v['userId'];
            $res['lists'][$k]['userData'] = $userModel->whereIn('user_id',$ids)->firstHumpArray();
        }

        return $res;

    }

    //后台删除留言反馈

    public static function deleteUserMessage($data){
        $userMessageModel = new UserMessageModel();
        $bool = $userMessageModel->where('user_message_id',$data['userMessageId'])->delete();
        if (empty($bool)){
            throw new RJsonError('删除失败','MESSAGE_ERROR');
        }
        return ;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\User;

use App\Logic\Common\ShoppingLogic;
use App\Logic\Shopping\Api\Collection\CollectionLogic;
use App\Model\Shopping\User\UserModel;
use App\Model\Shopping\UserMessage\UserMessageModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserLogic extends ShoppingLogic
{

    public static function showUser() {
        $userModel = new UserModel();
//        $collection = new CollectionLogic();
        $data['userId'] = \Session::get('userId');
        $res = $userModel->where('user_id',$data['userId'])->firstHumpArray();
//        $res['collection'] = $collection->showCollection($res, $res['userId']);

        return ['data' => $res];
    }

    public static function addMessage($data=array()){
        $userModel = new UserModel();
        $userMessageModel = new UserMessageModel();


        $messageData['user_message_content'] = empty($data['userMessageContent']) ? '' : $data['userMessageContent'];

        $messageData['created_at'] = time();


        if (!empty($messageData['user_message_content'])){
            $len = mb_strlen($messageData['user_message_content']);
            if ($len > 200){
                throw new RJsonError('字数不能超出200哦','MESSAGE_ERROR');
            } elseif ($len <= 200) {
                $messageData['user_id'] = \Session::get('userId');
                $bool = $userMessageModel->setDataByArray($messageData)->save();
                if (empty($bool)){
                    throw new RJsonError('意见反馈失败','MESSAGE_ERROR');
                }
            }
        } elseif ($messageData['user_message_content'] == ''){
            $messageData['user_id'] = \Session::get('userId');
            $bool = $userMessageModel->setDataByArray($messageData)->save();
            if (empty($bool)){
                throw new RJsonError('意见反馈失败','MESSAGE_ERROR');
            }
        }
    }
}

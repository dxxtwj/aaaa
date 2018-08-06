<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shangrui\Admin\User;

use App\Logic\Common\ShoppingLogic;
use App\Model\Shangrui\User\UserModel;
use App\Model\Shangrui\UserMessage\UserMessageModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;


class UserLogic extends ShoppingLogic
{
    // 查看用户反馈
    public static function showUserMessage($data = array()){
        $userModel = new UserModel();
        $userMessageModel = new UserMessageModel();

        $res = $userMessageModel->orderBy('app_user_message.created_at','DESC')->getDdvPageHumpArray();

        foreach ($res['lists'] as $k => $v){
            $ids[] = $v['userId'];
            $res['lists'][$k]['userData'] = $userModel->whereIn('user_id',$ids)->firstHumpArray();
        }

        return $res;
    }


    // 删除用户反馈
    public  static function deleteUserMessage($data = array()){
        $userMessageModel = new UserMessageModel();
        $bool = $userMessageModel->where('user_message_id',$data['userMessageId'])->delete();

        if (!$bool){
            throw new RJsonError('删除用户留言失败','MESSAGE_ERROR');
        }

        return ;
    }
}
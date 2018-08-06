<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/16
 * Time: 下午2:52
 */

namespace App\Logic\V3\Open\Wechat;

use EasyWeChat\Kernel\Messages\Text;
use \App\Logic\V3\Common\Wechat\OpenPlatformCommonLogic;
use \App\Logic\V3\Common\Wechat\OpenAppWechatCommonLogic;

class OpenPlatformLogic extends OpenPlatformCommonLogic
{

    /**
     * 接到的异常请再次抛出
     * @param $e
     */
    public function ticketHandlerException($e){
        throw $e;
    }

    /**
     * 处理授权成功事件
     * @param $message
     * @throws \App\Logic\Exception
     * @throws \ReflectionException
     */
    public function ticketAuthorized($message){

        // $message 为微信推送的通知内容，不同事件不同内容，详看微信官方文档
        // 获取授权公众号 AppId： $message['AuthorizerAppid']
        // 获取 AuthCode：$message['AuthorizationCode']
        // 然后进行业务处理，如存数据库等...
        \Log::info('EVENT_AUTHORIZED');
        \Log::info($message);
        $authLogic = new OpenAppWechatCommonLogic([
            'wechatAppId'=> $message['AuthorizerAppid'],
            'queryAuthCode'=> $message['AuthorizationCode'],
            'preAuthCode'=> $message['PreAuthCode'],
            'authCodeAt'=> $message['CreateTime'],
            'authCodeExpiredAt'=> $message['AuthorizationCodeExpiredTime'],
            'authState' => 1
        ]);
        // 更新授权信息到数据库
        $authLogic->update();
        // 开启定时任务
        $authLogic->addTimingTasksJob();
    }

    /**
     * 处理授权取消事件
     * @param $message
     * @throws \App\Logic\Exception
     * @throws \ReflectionException
     */
    public function ticketUnauthorized($message){
        // ...
        \Log::info('EVENT_UNAUTHORIZED');
        \Log::info($message);
        $authLogic = new OpenAppWechatCommonLogic([
            'wechatAppId'=> $message['AuthorizerAppid'],
            'authCodeExpiredAt'=> time(),
            'authState' => 0
        ]);
        // 更新授权信息到数据库
        $authLogic->update();

    }

    /**
     * 处理授权更新事件
     * @param $message
     * @throws \App\Logic\Exception
     * @throws \ReflectionException
     */
    public function ticketUpdateauthorized($message){

        \Log::info('EVENT_UPDATE_AUTHORIZED');
        \Log::info($message);
        $authLogic = new OpenAppWechatCommonLogic([
            'wechatAppId'=> $message['AuthorizerAppid'],
            'queryAuthCode'=> $message['AuthorizationCode'],
            'preAuthCode'=> $message['PreAuthCode'],
            'authCodeAt'=> isset($message['CreateTime'])?$message['CreateTime']:time(),
            'authCodeExpiredAt'=> $message['AuthorizationCodeExpiredTime'],
            'authState' => 1
        ]);
        // 更新授权信息到数据库
        $authLogic->update();
        // 开启定时任务
        $authLogic->addTimingTasksJob();
    }
}
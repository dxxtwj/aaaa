<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/16
 * Time: 下午2:52
 */

namespace App\Logic\V3\Open\Wechat;

use App\Model\V1\Open\OpenPaymchWechatModel;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

class OpenPlatform
{
    /**
     * @param $mchId
     * @return \EasyWeChat\OpenPlatform\Application
     * @throws \App\Model\Exception
     */
    public static function getOpenPlatform(){

        $config = config('wechat.open');

        $openPlatform = Factory::openPlatform($config);
        return $openPlatform;
    }
}
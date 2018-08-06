<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/31
 * Time: 下午8:45
 */

namespace App\Logic\V3\Common\Wechat;

use App\Logic\Exception;
use App\Logic\V3\Common\LoadDataLogic;
use EasyWeChat\Factory;

class OpenPlatformCommonLogic extends LoadDataLogic
{
    // 配置文件
    protected $config = [];
    /**
     * @var \EasyWeChat\OpenPlatform\Application|null $openPlatform
     */
    protected $openPlatform = null;

    /**
     * @param null $config
     * @param bool $isReload
     * @return $this
     * @throws Exception
     */
    public function configInit($config = null, $isReload = false){
        if ($isReload===false&&(!empty($this->config))){
            // 直接配置文件
            return $this;
        }
        if (empty($config))
        {
            // 试图读取配置信息的config
            $config = config('wechat.open');
        }
        if (empty($config)){
            // 如果没有找到配置文件
            throw new Exception('没有找到开放平台的配置', 'CONFIG_NOT_FIND');
        }
        //缓存起来
        $this->config = $config;
        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getConfig(){
        if (empty($this->config)){
            // 如果没有找到配置文件
            throw new Exception('没有找到开放平台的配置', 'CONFIG_NOT_FIND');
        }
        return $this->config;
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Application|null
     * @throws Exception
     */
    public function openPlatform(){
        if (empty($this->openPlatform)){
            $this->configInit();
            $this->openPlatform = Factory::openPlatform($this->config);
        }
        return $this->openPlatform;
    }

    /**
     * @return \EasyWeChat\OpenPlatform\Application|null
     * @throws Exception
     */
    public static function getOpenPlatform(){
        return self::getOpenPlatformLogic()->openPlatform();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/17
 * Time: 下午12:59
 */

namespace App\Http\Controllers\V3\Api\Web\Open\Wechat;

use App\Http\Controllers\Controller;
use App\Logic\V3\Open\Wechat\OpenAppWechatLogic;

class JssdkController extends Controller
{
    /**
     * @return array
     * @throws \App\Logic\Exception
     * @throws \DdvPhp\DdvRestfulApi\Exception\RJsonError
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getConfigSign(){
        $this->validate(null, [
            'url' => 'required|string',
        ]);
        $OpenAppWechatLogic = new OpenAppWechatLogic();
        $OpenAppWechatLogic->load($this->verifyData);
        $result = $OpenAppWechatLogic->getJsConfigSign();
        return [
            'data'=>[
                'jsConfig' => $result
            ]
        ];

    }

}
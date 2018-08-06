<?php
namespace App\Logic\Wechat;

use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use App\Thirdparty;
/*
 * 扫码付款逻辑层
 */
class WechatPayLogic
{
    const APPID = '123';
    const KEY = '192006250b4c09247ec02edce69f6a2d';// key为商户平台设置的密钥key
    /*
     * 扫码付款
     * @param array $data  生成二维码所需参数
     */
    public static function scanCodePay($data=array()) {

        // 模拟数据
        $data = [
            'appid' => self::APPID,
            'mch_id' => '123',//商户号
            'time_stamp' => time(),//时间戳
            'nonce_str' => md5(time()),//随机字符串
            'product_id' => '1',//商品ID
        ];

        $data['sign'] = self::getSign($data, 1);//获取签名，拼接key

        $strUrl = self::url($data, 1);//转换为url格式
        $url = 'weixin：//wxpay/bizpayurl?'.$strUrl.'';
    }

    /*
     * 获取签名
     * @param int $type 1=需要转码 2=不需要转码
     * @return sting $strUrl 返回签名
     */
    public static function getSign($data, $type = 1) {

        array_filter($data);// 参数不为空，去除空值

        ksort($data);// 排序

        $strUrl = self::url($data, $type).'&key='.self::KEY;// 拼接url格式

        $strUrl = strtoupper(md5($strUrl)); // 加密字符串并且转换为大写并且

        return $strUrl;
    }

    /*
     *  把字符串拼接成url格式的方法
     *  @param int $type 1=需要转码   2=不需要转码
     *  @return sting 返回拼接完成后的字符串
     */
    public static function url($data, $type) {

        if ((int)$type === 1) {// 需要转码

            return $strUrl = urldecode(http_build_query($data));

        } elseif((int)$type === 2) {// 不需要转码

            return $strUrl = http_build_query($data);
        }
    }

}

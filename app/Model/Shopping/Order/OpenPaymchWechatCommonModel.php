<?php

namespace App\Model\Shopping\Order;

use \App\Logic\Exception;
use App\Model\Model;

class OpenPaymchWechatCommonModel extends Model
{
    protected $table = 'shopping_open_paymch_wechat';
    protected $primaryKey = 'mch_id';

    /**
     * 获取商户配置信息
     * @param $siteId
     * @param string $mchId
     * @return mixed
     * @throws Exception
     * @throws NextException
     */
    public function getConfig($mchId = ''){
        if (empty($mchId)){
            throw new Exception('商户号必须输入', 'MCH_ID_MUST_INPUT');
        }
        //得到微信付款的信息
        $wechatKey = $this->where('wechat_mch_id', $mchId)->firstHump();
        if (empty($wechatKey)){
            throw new Exception('商户信息暂未开通', 'NOT_FIND_KEY');
        }
        //得到一个临时的文件，文件名。
        $keyPath = tempnam(sys_get_temp_dir(), 'apiclient_key');

        $fp = @fopen($keyPath, "w");
        @fwrite($fp, $wechatKey->sslKey);
        fclose($fp);
        $certPath = tempnam(sys_get_temp_dir(), 'apiclient_cert');
        $fp = @fopen($certPath, "w");
        @fwrite($fp, $wechatKey->sslCert);
        fclose($fp);
        $wechatKey->setDataByArray([
            'certPath' => $certPath,
            'keyPath' => $keyPath
        ]);

        return $wechatKey;
    }
}
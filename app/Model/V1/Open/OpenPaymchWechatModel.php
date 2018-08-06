<?php

namespace App\Model\V1\Open;

use App\Logic\V2\Pay\NextException;
use App\Model\Exception;
use App\Model\Model;
use App\Model\V1\Site\SiteModel;

class OpenPaymchWechatModel extends Model
{
    protected $table = 'open_paymch_wechat';
    protected $primaryKey = 'mch_id';
    protected $dateFormat = 'U';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

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
        /*    if (empty($siteId)){
                throw new Exception('站点ID不能为空','SITEID_NOT_FIND');
            }
            $site = (new SiteModel())->where('site_id', $siteId)->firstHump();
            if(empty($site)){
                throw new Exception('找不到该站点', 'SITE_NOT_FIND');
            }
            if(empty($site->wechatPayMchId)){
                throw new NextException('该站点暂未绑定微信收款商户号', 'WX_MCHID_NOT_FIND');
            }
            $mchId = $site->wechatPayMchId;*/
        }
        $wechatKey = $this->where('wechat_mch_id', $mchId)->firstHump();
        if (empty($wechatKey)){
            throw new Exception('商户信息暂未开通', 'NOT_FIND_KEY');
        }

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
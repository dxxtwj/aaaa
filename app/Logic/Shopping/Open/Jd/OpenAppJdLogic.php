<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/4/9
 * Time: 下午7:58
 */

namespace App\Logic\V3\Open\Jd;


use App\Logic\Exception;
use App\Model\V3\Common\DomainCommonModel;
use DdvPhp\DdvUrl;
use DdvPhp\Jd\JdSdk;

class OpenAppJdLogic
{
    // 京东的auth_app_id
    protected $jdAppId = '';
    // 应用授权登录
    const PUBLIC_APP_AUTH = 'https://oauth.jd.com/oauth/authorize';

    /**
     * @param null $query
     * @param array $scopes
     * @return array
     * @throws Exception
     */
    public function handleWebAuthorizationUrl($query = null, $scopes=[]){
        if (empty($query['code'])){
            throw new Exception('没有code码', 'NOT_FIND_AUTH_CODE');
        }
        JdSdk::init();
        $c= JdSdk::getJdClient([
            'appKey' => "B9330AF9CE41A8582A312D9D34A93268",
            'appSecret' => "18ab3f3090f84bae8e70c8abe625b393",
            'accessToken' => "7cad5996-5cbd-406a-8d87-ed63c81413b4",
            'serverUrl' => "https://api.jd.com/routerjson"
            //'serverUrl' => "http://gw.api.360buy.net/routerjson"
        ]);

        $req = new \LdopAlphaWaybillQueryRequest;
        $req->setProviderCode("jingdong");
        $req->setWaybillCode("jingdong");
        $resp = $c->execute($req, $c->accessToken);
        print(json_encode($resp));
        var_dump(65,$query['code'],55);
        die;
        return [];
    }

    /**
     * @param null $callback
     * @param array $scopes
     * @return string
     * @throws Exception
     */
    public function getWebAuthorizationUrl($callback = null, array $scopes = []){
        // 把回调地址解析为数组
        $urlObj =  DdvUrl::parse($callback);
        // 如果地址参数不为空
        if (!empty($urlObj['query'])){
            // 解析参数为数组
            $urlQueryObj = DdvUrl::parseQuery($urlObj['query']);
            //
            /*if ($urlQueryObj){
                unset($urlQueryObj['app_id']);
            }*/
            $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        }
        // 重新编译回调地址
        $callback = DdvUrl::build($urlObj);
        // 构建一个跳转地址数组
        $urlObj =  DdvUrl::parse(self::PUBLIC_APP_AUTH);

        $this->getAppIdByCallback($callback);

        $urlQueryObj = [
            'client_id'=>$this->jdAppId,
            'state'=>'',
            'response_type'=>'code',
            'scope'=>implode(',', $scopes),
            'redirect_uri'=>$callback
        ];
        $urlObj['query'] = DdvUrl::buildQuery($urlQueryObj);
        $url = DdvUrl::build($urlObj);
        return $url;

    }

    /**
     * 获取支付宝应用appid-通过授权回调地址[域名]
     * @param $callback
     * @return string
     * @throws Exception
     *
     */
    public function getAppIdByCallback($callback){
        $this->jdAppId = 'B9330AF9CE41A8582A312D9D34A93268';
        return;
        $urlArr = DdvUrl::parse($callback);
        $domain = $urlArr['host'];
        $resDomain = (new DomainCommonModel())->where(['domain' => $domain])->select('jd_app_id')->first();
        if(empty($resDomain)){
            throw new Exception('没有找到该域名', 'DOMAIN_NOT_FIND');
        }
        if(empty($resDomain->jd_app_id)){
            throw new Exception('没有找到该支付宝appid', 'APPID_NOT_FIND');
        }
        $this->jdAppId = $resDomain->jd_app_id;
        return $this->jdAppId;
    }
}
<?php
namespace App\Logic\AliPay;

use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use OSS\OssClient;

class AliPayLogic
{
    public $aliPayConfig = array();

    /*
     * 获取公共参数
     */
    public function __construct() {

        date_default_timezone_set('PRC');
        $this->aliPayConfig = config('alipay.openApi');
    }

    /*
     * ali 移动端支付
     * 新版本 rsa2
     * @param array $array 业务参数
     * @param string $url 这个是要跳转的，用来跳转到支付宝接口
     */
    public function phoneAliPay($array) {

        //  公共参数
        $data['app_id'] = $this->aliPayConfig['appId'] ? $this->aliPayConfig['appId'] : '';
        $data['method'] = 'alipay.trade.wap.pay';//接口名称
        $data['return_url'] = $this->aliPayConfig['returnUrl'] ? $this->aliPayConfig['returnUrl'] : '';//同步返回地址
        $data['notify_url'] = $this->aliPayConfig['returnUrl'] ? $this->aliPayConfig['returnUrl'] : '';//异步返回地址
        $data['charset'] = 'UTF-8';//请求使用的编码格式
        $data['sign_type'] = 'RSA2';
        $data['sign'] = '';
        $data['timestamp'] = date('Y-m-d H:i:s');//发送请求的时间
        $data['version'] = '1.0';//调用的接口版本，固定为：1.0

        //业务参数,以下业务参数会是前段传过来,现在只是模拟数据
        $yeWuData['out_trade_no'] = date('YmdHis');//商户订单号
        $yeWuData['product_code'] = 'FAST_INSTANT_TRADE_PAY';//销售产品码

        $yeWuData['total_amount'] = 0.01;//订单总金额
        $yeWuData['subject'] = '测试';//订单标题
        $yeWuData['body'] = '测试订单';//订单描述
        //  $yeWuData['timeout_express'] = '1';//该笔订单允许的最晚付款时间

        $data['biz_content'] = json_encode($yeWuData, JSON_UNESCAPED_UNICODE);
        $data = self::getSign($data);

        $url = $this->aliPayConfig['gatewayUrl'].'?'.self::url($data, 2);//拼接网关，会获取到一个地址，这个地址就是支付宝支付

        var_dump($url);
        header('Location:'.$url);
    }

    /*
     * ali 电脑版 支付
     * 新版本 rsa2
     * @param array $array 业务参数
     * @param string $url 这个是要跳转的，用来跳转到支付宝接口
     */
    public function pcAliPay($array) {

        //  公共参数
        $data['app_id'] = $this->aliPayConfig['appId'] ? $this->aliPayConfig['appId'] : '';
        $data['method'] = 'alipay.trade.page.pay';//接口名称
        $data['return_url'] = $this->aliPayConfig['returnUrl'] ? $this->aliPayConfig['returnUrl'] : '';//同步返回地址
        $data['notify_url'] = $this->aliPayConfig['returnUrl'] ? $this->aliPayConfig['returnUrl'] : '';//异步返回地址
        $data['charset'] = 'UTF-8';//请求使用的编码格式
        $data['sign_type'] = 'RSA2';
        $data['sign'] = '';
        $data['timestamp'] = date('Y-m-d H:i:s');//发送请求的时间
        $data['version'] = '1.0';//调用的接口版本，固定为：1.0

        //业务参数,以下业务参数会是前段传过来,现在只是模拟数据
        $yeWuData['out_trade_no'] = date('YmdHis');//商户订单号
        $yeWuData['product_code'] = 'FAST_INSTANT_TRADE_PAY';//销售产品码
        $yeWuData['total_amount'] = 0.01;//订单总金额
        $yeWuData['subject'] = '测试';//订单标题
        $yeWuData['body'] = '测试订单';//订单描述
//        $yeWuData['timeout_express'] = '1';//该笔订单允许的最晚付款时间

        $data['biz_content'] = json_encode($yeWuData, JSON_UNESCAPED_UNICODE);
        $data = self::getSign($data);

        $url = $this->aliPayConfig['gatewayUrl'].'?'.self::url($data, 2);//拼接网关，会获取到一个地址，这个地址就是支付宝支付
        var_dump($url);
        header('Location:'.$url);


    }

    /*
     * 支付宝退款接口,只支持新版本 rsa2
     */
    public function refundAliPay() {

        $this->ossText();
    }

    /*
     * 这个方法是对oss 的研究
     */
    public function ossText() {
        //      @param string $ossConfig['endpoint'] 您选定的OSS数据中心访问域名，例如oss-cn-hangzhou.aliyuncs.com
        $ossConfig = config('aliyun.oss');

        $aliOss = new OssClient($ossConfig['accessKeyId'], $ossConfig['accessKeySecret'], $ossConfig['endpoint']);
//        $data = $aliOss->listBuckets();

//        三种权限分别对应OSSClient::OSS_ACL_TYPE_PRIVATE，私有读写
//        OssClient::OSS_ACL_TYPE_PUBLIC_READ, 公共读私有写
//        OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE 公共读写
        $bucketName = 'text123321';
        $fileName = '123ss1';
        $file = '/Users/easyke1/Documents/94FD0DC3-F95F-4DA4-9850-AC51AD9D3C8C.png';

//        $file2 = '/Users/easyke1/Documents/QQ20180411-164248.png';

//        $file3 = '/Users/easyke1/Documents/QQ20180411-165747.jpg';

//        $a = $aliOss->createBucket($bucketName, OSS_ACL_TYPE_PRIVATE);// 1、 创建bucket

//        $a = $aliOss->doesBucketExist($bucketName);//查看这个 bucket存不存在   return bool

//        $a = $aliOss->listBuckets(); //  列出所有bucket

//        $a = $aliOss->deleteBucket('这里写bucket名字');// 删除bucket

//        $a = $aliOss->createObjectDir($bucketName, $fileName.'/'); // 创建目录

//        $a = $aliOss->putObject($bucketName, $fileName.'/gg', 'contents');//  字符串上传  1、 2、文件名  3、内容 会得到一个下载文件的链接

//        $a = $aliOss->uploadFile($bucketName, $fileName.'/gg', $file);// 上传本地文件

//        $a = $aliOss->signUrl($bucketName, $fileName);// 获取上传 图片的路径


//        var_dump($a);
    }



    /*
     *  获取带下标 的签名;处理过的
     */
    public function getSign($arr) {
        $str = self::getStr($arr);
        $arr['sign'] = self::rsaSign($str, $this->aliPayConfig['merchantPrivateKey'], 'RSA2');//生成签名

        return $arr;
    }
    /*
     * 获取处理过的字符串并且转换为url格式
     */
    public static function getStr($arr) {
        $arr = array_filter($arr);

        if (!empty($arr['sign'])) {

            unset($arr['sign']);
        }
        ksort($arr);

        return self::url($arr, 1);
    }

    /*
     * 转换为url格式
     */
    public static function url($arr, $type = 1 ) {

        if ((int)$type === 1) {

            return urldecode(http_build_query($arr));

        } elseif ((int)$type === 2) {

            return http_build_query($arr);

        }

    }

    /**
     * 生成签名
     * RSA签名
     * @param $data 待签名数据
     * @param $private_key 私钥字符串
     * return 签名结果
     */
    public static function rsaSign($data, $private_key,$type = 'RSA') {

        $type = strtoupper($type);

        $search = [
            "-----BEGIN RSA PRIVATE KEY-----",
            "-----END RSA PRIVATE KEY-----",
            "\n",
            "\r",
            "\r\n"
        ];

        $private_key=str_replace($search,"",$private_key);
        $private_key=$search[0] . PHP_EOL . wordwrap($private_key, 64, "\n", true) . PHP_EOL . $search[1];
        $res=openssl_get_privatekey($private_key);

        if($res)
        {
            if($type == 'RSA'){
                openssl_sign($data, $sign,$res);
            }elseif($type == 'RSA2'){
                //OPENSSL_ALGO_SHA256
                openssl_sign($data, $sign,$res,OPENSSL_ALGO_SHA256);
            }
            openssl_free_key($res);
        } else {

            throw new RJsonError('私钥格式有误!', 'PRIVATE_ERROR');
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 验证签名
     * RSA验签
     * @param $data 待签名数据
     * @param $public_key 公钥字符串
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    public static function rsaCheck($data, $public_key, $sign,$type = 'RSA')  {

        $type = strtoupper($type);

        $search = [
            "-----BEGIN PUBLIC KEY-----",
            "-----END PUBLIC KEY-----",
            "\n",
            "\r",
            "\r\n"
        ];
        $public_key=str_replace($search,"",$public_key);
        $public_key=$search[0] . PHP_EOL . wordwrap($public_key, 64, "\n", true) . PHP_EOL . $search[1];
        $res=openssl_get_publickey($public_key);
        if($res)
        {
            if($type == 'RSA'){
                $result = (bool)openssl_verify($data, base64_decode($sign), $res);
            } elseif ($type == 'RSA2'){
                $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_SHA256);
            }
            openssl_free_key($res);
        } else {
            throw new RJsonError('公钥格式有误!', 'PUBLIC_ERROR');
        }
        return $result;
    }

    /*
     * @ curl 请求,post get 都可以
     * @ param string $url 要拉取的地址
     * @ param string $type 类型
     * @ param array $data 数据
     * @ return curl_errno  返回拉取失败的原因
     * @ return json $optput 返回拉取的数据
     */
    public function http_curl($url, $data, $type='GET') {

        // 初始化
        $ch = curl_init();

        // 设置参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);
        $type = strtoupper($type);//转换为大写

        if ((string)$type === 'POST') {

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        }

        //  采集
        $output = curl_exec($ch);

        // 关闭
        curl_close($ch);

        if (curl_errno($ch)) {// 报错

            return curl_errno($ch);

        } else {

            return json_decode($output, true);
        }

    }

}

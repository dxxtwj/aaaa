<?php

namespace App\Http\Controllers\Shopping\Admin\WechatRefund;

use \App\Http\Controllers\Controller;
use App\Logic\Shopping\Admin\WechatRefund\WechatRefundLogic;
use App\Model\Shopping\Order\OrderModel;
use App\Model\Shopping\Refund\RefundModel;
use App\Model\Shopping\WechatNotify\WechatNotifyModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class WechatRefundController extends Controller
{

    const APPID = 'wx3d1656447b0c8f6a';// appid
    const MCHID = '1508528661';//商户号
    const KEY = '09393a5d86f49b719a576512a2b83d1c';//商户key
    const REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';//微信退款接口地址
    const REFUNDURL = 'https://api.mch.weixin.qq.com/pay/refundquery';//微信退款查询接口地址

    /*
     * 生成签名
     * @param array 一位数组
     * @return string url
     */
    public static function getSign($arr) {
        if (!empty($arr['sign'])) {//删除签名
            unset($arr['sign']);
        }
        $arr =  array_filter($arr);//去除空值
        ksort($arr);//键名排序
        $url = self::getUrl($arr, 1).'&key='.self::KEY;//生成URL格式字符串,拼接KEY
        $str = strtoupper(md5($url));//MD5加密后转换大小写
        return $str;

    }
    /*
     * 生成url格式字符串
     * @param int $type 等于0 需要编码  不等于0不需要编码
     * @return string url地址
     */
    public static function getUrl($arr, $type=0) {
        if ($type == 0) {// 需要转码
            return http_build_query($arr);
        } elseif ($type==1) {//不需要转码
            return urldecode(http_build_query($arr));
        }
    }
    /*
     * 获取代签名的数组
     * @param array 一位数组
     * return array 追加了签名的数组
     */
    public static function setSign($arr) {
        $sign = self::getSign($arr);
        $arr['sign'] = $sign;
        return $arr;
    }

    /*
     * 验证签名
     * @param array 一位数组
     * return bool
     */
    public static function chekSign($arr=array()) {
        $sign = self::getSign($arr);
        if ($sign == $arr['sign']) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 获取随机数
     * @param int $length 长度
     * @return string
     */
    public static function getStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    /*
     * 数组转换为XML格式
     * @param array 要转换的数组
     * @return xml 转换后的数据
     */
    public static function arrayToXml($arr) {
        if(!is_array($arr) || count($arr) == 0) return '';
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /*
     * XML格式转换为数组
     * @param xml 要转换的xml
     * @return array 转换后的数据
     */
    public static function xmlToArray($xml) {
        if($xml == '') return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }
    /*
     * post xml 发送数据
     * @param string $url
     * @param xml|array $postfields
     * @param int $status 是否有证书  1 是  0 否
     * @return mixed 拉取回的数据
     */
    public static function postXml($url, $postfields, $status=0) {
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $postfields;

        //禁用证书验证
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;

        if ($status == 1) {

            //证书代码
            $params[CURLOPT_SSLCERTTYPE] = 'PEM';
            $params[CURLOPT_SSLCERT] = '/disk2/www/auto-make-station-api/public/certificate2/apiclient_cert.pem';
            $params[CURLOPT_SSLKEYTYPE] = 'PEM';
            $params[CURLOPT_SSLKEY] = '/disk2/www/auto-make-station-api/public/certificate2/apiclient_key.pem';

        }
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接

        return $content;
    }

    /*
     * 发送微信退款
     */
    public function refundWechat() {
        $this->verify(
            [
                'orderId' => '',//订单ID
                'refundFee' => 'no_required',//要退多少
                //'contents' => 'no_required',//退款理由
                //'explain' => 'no_required',//退款补充说明
            ]
            , 'GET');
        $wechatNotifyModel = new WechatNotifyModel();
        $wechatNotifyData = $wechatNotifyModel
            ->where('wechat_out_trade_no', $this->verifyData['orderId'])
            ->firstHumpArray();

        if (empty($wechatNotifyData)) {
            \Log::info('没有该订单');
            throw new RJsonError('没有该订单','ORDER_ERROR');
        }
        $orderModel = new OrderModel();
        $orderData = $orderModel->where('order_id', $this->verifyData['orderId'])->firstHumpArray();

        $parameter = [
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'nonce_str' => self::getStr(),
            'transaction_id' => $wechatNotifyData['wechatTransactionId'],
            'out_refund_no' => self::getStr(),//退款单号不能一样
            'total_fee' => $wechatNotifyData['wechatTotalFee'],
            'refund_fee' => empty($this->verifyData['refundFee']) ? $wechatNotifyData['wechatTotalFee'] : $this->verifyData['refundFee'] * 100,
            'out_trade_no' => $wechatNotifyData['wechatOutTradeNo'],
            'refund_desc' => empty($orderData['refundContents']) ? '' : $orderData['refundContents'],
        ];
        $parameter = self::setSign($parameter);
        $xmlData = self::arrayToXml($parameter);
        $wechatReturn = self::postXml(self::REFUND,$xmlData,1);
        $arrayWechatReturn = self::xmlToArray($wechatReturn);

        if ($arrayWechatReturn['return_code'] == 'SUCCESS' && $arrayWechatReturn['result_code'] == 'SUCCESS') {

            \DB::beginTransaction();
            try {

                $refundModel = new RefundModel();
                $addData = [
                    'wechat_refund_id' => $arrayWechatReturn['refund_id'],
                    'wechat_return_code' => $arrayWechatReturn['return_code'],
                    'wechat_return_msg' => $arrayWechatReturn['return_msg'],
                    'wechat_appid' => $arrayWechatReturn['appid'],
                    'wechat_mch_id' => $arrayWechatReturn['mch_id'],
                    'wechat_nonce_str' => $arrayWechatReturn['nonce_str'],
                    'wechat_sign' => $arrayWechatReturn['sign'],
                    'wechat_result_code' => $arrayWechatReturn['result_code'],
                    'wechat_transaction_id' => $arrayWechatReturn['transaction_id'],
                    'wechat_out_trade_no' => $arrayWechatReturn['out_trade_no'],
                    'wechat_out_refund_no' => $arrayWechatReturn['out_refund_no'],
                    'wechat_refund_fee' => $arrayWechatReturn['refund_fee'],
                    'wechat_coupon_refund_fee' => $arrayWechatReturn['coupon_refund_fee'],
                    'wechat_total_fee' => $arrayWechatReturn['total_fee'],
                    'wechat_cash_fee' => $arrayWechatReturn['cash_fee'],
                    'wechat_coupon_refund_count' => $arrayWechatReturn['coupon_refund_count'],
                    'wechat_cash_refund_fee' => $arrayWechatReturn['cash_refund_fee'],
                    'wechat_contents' => empty($orderData['refundContents']) ? '' : $orderData['refundContents'],
                    'wechat_explain' => empty($orderData['refundExplain']) ? '' : $orderData['refundExplain'],//补充说明
                ];
                $refundModel->setDataByHumpArray($addData)->save();

                \Log::info($arrayWechatReturn);

                // 退款完后更改状态
                $data['refundStatus'] = 2;//退款状态  2 完成
                $data['orderStatus'] = 3; // 订单状态  3 完成
                $orderModel->where('order_id',$this->verifyData['orderId'])->updateByHump($data);


                \DB::commit();
            } catch(QueryException $e) {

                \DB::rollBack();
                \Log::info('订单ID为:'.$this->verifyData['orderId'].'退款失败');
                throw new RJsonError($e->getMessage(), 'REFUND_ERROR');
            }

        } else {
            \Log::info('订单ID为:'.$this->verifyData['orderId'].'退款失败');
            \Log::info('订单ID为:'.$this->verifyData['orderId'].'退款失败的原因:'.$arrayWechatReturn['err_code_des']);
            throw new RJsonError($arrayWechatReturn['err_code_des'], 'REFUND_ERROR');
        }

    }


    /*
     * 拒接退款接口
     * @param int status 灵活传 0: 无操作  1：退款中  2：退款成功 3 退款失败
     * @return ;
     */
    public function editOrder() {
        $this->verify(
            [
                'orderId' => '',//订单ID
                'contents' => 'no_required',//是否接受退款 1 接受  2拒接
            ]
            , 'POST');
        $res = WechatRefundLogic::editOrder($this->verifyData);
        return $res;
    }



    /*
     * 查询退款接口
     */
    public function refundWechatShow() {

        $this->verify(
            [
                'orderId' => '',//订单ID
            ]
            , 'GET');

        $param = [

            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'nonce_str' => self::getStr(),
            'out_trade_no' => $this->verifyData['orderId'],
        ];

        $param = self::setSign($param);
        $xmlData = self::arrayToXml($param);
        $wechatXml = self::postXml(self::REFUNDURL,$xmlData);
        $array = self::xmlToArray($wechatXml);
        return ['data' => $array];
    }
}
<?php

namespace App\Http\Controllers\Shangrui\Api\WechatPay;

use \App\Http\Controllers\Controller;
use App\Logic\Common\ShoppingLogic;
use App\Logic\V0\Wechat\WechatAppLogic;
use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Order\OrderModel;
use App\Model\Shangrui\OrderGoods\OrderGoodsModel;
use App\Model\Shangrui\User\UserModel;
use App\Model\Shangrui\WechatNotify\WechatNotifyModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Monolog\Handler\IFTTTHandler;
use PhpOffice\PhpSpreadsheet\Reader\Xls\MD5;

class WechatPayController extends Controller
{
    public $status = [ //支付状态 1代付款 2 已付款 3已完成 -1 取消订单
        '0' => '1',
        '1' => '2',
        '2' => '3',
        '3' => '-1',
    ];

    const APPID = 'wx3d1656447b0c8f6a';  // appid
    const MCHID = '1508528661';  //商户号
    const KEY = '09393a5d86f49b719a576512a2b83d1c';   //商户key
    const NOTIFY = 'http://api.easyke.top/v1.0/wechatpay/notify';   // 支付回调地址
    const UNURL= 'https://api.mch.weixin.qq.com/pay/unifiedorder';  //统一下单地址
    const SHOWPAY = 'https://api.mch.weixin.qq.com/pay/orderquery';  //订单查询地址
    const APPWECHATPAY = 'weixin://wxpay/bizpayurl?';//微信二维码生成

    public function wechatPay(){
        $this->verify(
            [
                'orderId' => '', //订单ID
                'body' => 'no_requried', //描述
            ],'GET');
        date_default_timezone_set('PRC');  //设置默认时区
        $orderModel = new OrderModel();
        $where['order_id'] = $this->verifyData['orderId'];
        $orderData = $orderModel->where($where)->firstHumpArray();
        $this->verifyData['orderTotalPrice'] = $orderData['orderTotalPrice'];  //订单总价
        if ($orderData['created_at'] + 10000 < time()){  //订单超过三个小时，改为过期
            $updata['order_delete'] = 2;  //修改为删除
            $orderModel->where($where)->updateByHump($updata);
            throw new RJsonError('订单已经过期了，请重新下单','ORDER_ERROR');
        }
        $orderGoodsModel = new OrderGoodsModel();
        $orderGoodsData = $orderGoodsModel->where('order_id',$orderData['orderId'])->getHumpArray();
        foreach ($orderGoodsData as $k => $v){
            if ($this->verifyData['orderTotalPrice'] != ($v['goodsNumber'] * $v['goodsPrice'])){
                throw new RJsonError('订单ID为'.$orderData['orderId'].'金额有误','ORDER_ERROR');
            }
        }
        $this->verifyData['trade_type'] = 'JSAPI';  //  这里先做公众号支付
        $prepayId = self::getPrepayid($this->verifyData);  //微信返回的支付交易会话ID
        $jsonData = self::getJson($prepayId);  //返回给前端的json 数据
        return ['data' => $jsonData];
    }

    /*
     * 生成签名
     * @param array 一维数组
     * @return string url
     */
     public static function getSign($arr){
         if (!empty($arr['sign'])){  //删除签名
             unset($arr['sign']);
         }
         $arr = array_filter($arr);
         ksort($arr);  //键名排序
         $url = self::getUrl($arr,1).'&key='.self::KEY;  //生成url 字符串，拼接上key
         $str = strtoupper(MD5($url));  //MD5加密后转换为大写
         return $str;
     }

     /*
      * 生成url 格式字符串
      * @param int $type   等于0需要编码  不等于0不需要编码
      * @return string str
      */
    public static function getUrl($arr,$type=0){
        if ($type == 0){
            return http_build_query($arr);
        } elseif ($type == 1){
            return urldecode(http_build_query($arr));
        }
    }

    /*
     * 获取签名的数组
     * @param array 一维数组
     * return array 追加了签名的数组
     */
    public static function setSign($arr){
        $sign = self::getSign($arr);
        $arr['sign'] = $sign;
        return $arr;
    }

    /*
     * 验证签名
     * @param array 一维数组
     * return bool
     */
    public static function checkSign($arr = array()){
        $sign = self::getSign($arr);
        if ($sign == $arr['sign']){
            return true;
        } else {
            return false;
        }
    }

    /*
     * 获取用户的openId
     */
    public static function getOpenid(){
        $userModel = new UserModel();
        $userId = \Session::get('userId');
        if (empty($userId)){
            throw new RJsonError('没有登录','NO_ACCOUNT_LOGIN');
        }
        $openid = $userModel->where('user_id',$userId)->firstHumpArray(['user_openid']);
        return $openid;
    }

    /*
     * 统一下单
     */
    public static function unifiedOrder($data = array()){
        $detail = array();
        $orderGoodsModel = new OrderGoodsModel();
        $orderGoodsData = $orderGoodsModel->where('order_id',$data['orderId'])->firstHumpArray();

        foreach ($orderGoodsData as $k => $v){
            $detail[$k]['goods_id'] = $v['goodsId'];
            $detail[$k]['goods_name'] = $v['goodsName'];
            $detail[$k]['quantity'] = $v['goodsNumber'];
            $detail[$k]['price'] = $v['goodsPrice'];
        }

        // 1.构建原始数据
        $params = [
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'nonce_str' => self::getStr(25),
            'body' => '电子产品',
            'out_trade_no' => $data['orderId'], //商城订单号
            'total_fee' => (float)$data['orderTotalPrice'], //订单总金额
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //客户端IP
            'notify_url' => self::NOTIFY,  //回调地址
            'trade_type' => $data['trade_type'], //支付类型
            'product_id' => $data['orderId'],  //产品ID //订单号
            'openid' => self::getOpenid(),
            'version' => '1.0',
        ];
        $array['goods_detail'] = $detail;
        $params['detail'] = json_encode($array,JSON_UNESCAPED_UNICODE);
        $params = self::setSign($params); // 2. 加入签名
        $xmlData = self::arrayToXml($params);  // 3. 将数据转成xml格式
        \Log::info('统一下单');
        \Log::info($xmlData);
        $res = self::postXml(self::UNURL,$xmlData); // 4.发送xml格式数据到接口地址
        $arr = self::xmlToArray($res);  //将xml数据转换为数组，里面有prepay_id
        return $arr;

    }

    /*
     * 获取随机字符串
     *@param int $length 长度
     * @return string
     */
    public static function getStr($length = 16){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /*
     * 获取到prepay_id 预支付交易会话标识
     * $param string $oid 订单号
     * @return string 预支付ID
     */
    public static function getPrepayid($data){
        $arr = self::unifiedOrder($data);
        if (empty($arr['prepay_id'])){
            throw new RJsonError('prepay_id为空，支付失败','PAY_ERROR');
        }
        return $arr['prepay_id'];
    }
    /*
     * 获取公众支付所需要的数据
     * @param string  @prepayId 预支付会话标识 ，有效时间为2小时
     * @return array
     */
    public static function getJson($prepayId){
        $strTime = time();
        $params = [
            'appId' => self::APPID,
            'timeStamp' => (string)$strTime,
            'nonceStr' => self::getStr(16),
            'package' => 'prepay_id='.$prepayId,
            'signType' => 'MD5',
        ];
        $params['paySign'] = self::getSign($params);
        return $params;
    }

    /*
     * 将数组装换为xml格式
     * @params array 要转换的数组
     * @return xml转换后的数据
     */
    public static function arrayToXml($arr){
        if (!is_array($arr) || count($arr) == 0) return '';
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
     * @param xml $postfields
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



        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接

        return $content;
    }
    /*
     *
     * 支付成功返回的参数
     * {"appid":"wxa34b8d82c86bd3c5",
     * "bank_type":"CFT",
     * "cash_fee":"1",
     * "fee_type":"CNY",
     * "is_subscribe":"Y",
     * "mch_id":"1492241632",
     * "nonce_str":"c27e2e4aaaa8c558396d892cb15d13ca",
     * "openid":"o2yi_1IHYM6kMDudv0bDTzLZw5T0",
     * "out_trade_no":"53",
     * "result_code":"SUCCESS",
     * "return_code":"SUCCESS",
     * "sign":"8F921DDA7FC532E17764B979CBF6054B",
     * "time_end":"20180524112530",
     * "total_fee":"1",
     * "trade_type":"JSAPI",
     * "transaction_id":"4200000112201805249749892935"}
     *
     * 异步通知地址
     * 1获取数据,会是一个xml格式的，把他转换为数组,是post的数据
     * 2验证签名 以前的方法
     * 3验证业务结果 return_code  和 result_code
     * 4验证订单号和金额 out_trade_no total_fee
     * 5 记录日志， 修改订单状态，给用户发货
     */
    public function wechatNotify(){
        \Log::info('测试支付异步会俩的');
        $postfields = file_get_contents('php://input');
        $arr = self::xmlToArray($postfields);
        \Log::info(json_encode($arr,true));
        $bool = self::checkSign($arr);
        if (empty($bool)){
            \Log::info('验证签名不通过');
            throw new RJsonError('验证签名不通过','SIGN_ERROR');
        }
        if ($arr['return_code'] == 'SUCCESS' && $arr['result_code'] == 'SUCCESS'){
            $orderModel = new OrderModel();
            $where['order_id'] = $arr['out_trade_no']; //订单ID
            $orderTotalprice = $orderModel->where($where)->firstHumpArray(['order_total_price']);

            if ($arr['total_fee'] = $orderTotalprice['orderTotalPrcie'] * 100){
                $returnParams = [
                    'return_code' => 'SUCESS',
                    'return_mes' => 'OK',
                ];

                // 2.将回调的支付信息存入数据库
                $wechatNotifyModel = new WechatNotifyModel();
                $wechatNotifyData = $wechatNotifyModel->where('wechat_out_trade_no',$arr['out_trade_no'])->firstHumpArray(['wechat_notify_id']);
                if (empty($wechatNotifyData['wechat_notify_id'])){
                    $NotifyData = [
                        'wechat_appid' => $arr['appid'],
                        'wechat_bank_type' => $arr['bank_type'],
                        'wechat_cash_fee' => $arr['cash_fee'],
                        'wechat_fee_type' => $arr['fee_type'],
                        'wechat_is_subscribe' => $arr['is_subscribe'],
                        'wechat_mch_id' => $arr['mch_id'],
                        'wechat_nonce_str' => $arr['nonce_str'],
                        'wechat_openid' => $arr['openid'],
                        'wechat_out_trade_no' => $arr['out_trade_no'],//订单ID
                        'wechat_result_code' => $arr['result_code'],
                        'wechat_return_code' => $arr['return_code'],
                        'wechat_sign' => $arr['sign'],
                        'wechat_time_end' => $arr['time_end'],
                        'wechat_total_fee' => $arr['total_fee'],
                        'wechat_trade_type' => $arr['trade_type'],
                        'wechat_transaction_id' => $arr['transaction_id'],
                    ];
                    $wechatNotifyModel->setDataByHumpArray($NotifyData)->save();
                    // 3.更改订单状态
                    $orderStart['order_status'] = $this->status[1];  //1代付款 2已付款 3已完成 -1取消订单
                    $orderModel->where($where)->updateByHump($orderStart);
                }
                $yingDa = self::arrayToXml($returnParams);
                echo exit("{$yingDa}");
            } else {
                \Log::info('订单ID:'.$where['order_id'].'金额有误');
                throw new RJsonError('订单ID:'.$where['order_id'].'金额有误', 'PRICE_ERROR');
            }
        } else {
            \Log::info('业务错误');
            throw new RJsonError('业务错误', 'PAY_ERROR');
        }
    }

    /*
     * 微信支付查询订单
     * @param string orderId 订单ID
     * @return array
     */
    public function showWechatPay(){
        $this->verify(
            [
                'orderId' => '', //订单ID
            ]
            ,'GET');
        $wechatNotifyModel = new WechatNotifyModel();
        $wechatOutTradeNo = $this->verifyData['orderId'];
        $wechatNotifyData = $wechatNotifyModel->where('wechat_notify_trade_no',$wechatOutTradeNo)->firstHumpArray();
        $params = [
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'transaction_id' => $wechatNotifyData['wechatTransactionId'],  //微信订单号
            'nonce_str' => md5(time()), //32为随机数
        ];
        $params = self::setSign($params);  //加入签名
        $xmlData = self::arrayToXml($params); // 转换为xml格式数据
        $showData = self::postXml(self::SHOWPAY,$xmlData);  //提交到查询接口，返回xml格式数据

        $array = self::xmlToArray($showData);  // 转换为数组
        if ($array['return_code'] == 'SUCCESS' && $array['return_msg'] == 'OK'){
            $data = self::tradeState($array);  //获取状态结果
            return ['data' => $data];
        } else {
            \Log::info('业务错误');
            throw new RJsonError('业务错误','WECHAT_ERROR');
        }
    }

    /*
     * 微信支付处理交易状态
     * @param array $array 回调的数据，这里面会有个交易状态
     * @return array
     */
    public function tradeState($array){
        switch ($array['trade_state']){ //交易状态
            case 'SUCCESS' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '支付成功';
                $orderModel = new OrderModel(); //更新数据库状态
                $orderStart['order_status'] = $this->status[1];   // 1待付款 2已付款 3已完成 -1取消订单
                $orderModel->where('order_id',$this->verifyData['orderId'])->updateByHump($orderStart);
                break;
            case 'REFUND' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '转入退款';
                break;
            case 'NOTPAY' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '未支付';
                break;
            case 'CLOSED' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '已关闭';
                break;
            case 'REVOKED' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '已撤销';
                break;
            case 'USERPAYING' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '用户支付中';
                break;
            case 'PAYERROR' :
                $data['tradeState'] = $array['trade_state'];
                $data['msg'] = '支付失败(其他原因，如银行返回失败)';
                break;
            default :
                $data = array();
                break;
        }
        return $data;
    }
}

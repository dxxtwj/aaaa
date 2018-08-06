<?php
namespace App\Logic\Shopping\Common\Payment;

use App\Logic\Exception;
use App\Logic\Shopping\Common\Exception\PaymentNext;
use App\Logic\Shopping\Common\Exception\PaymentNotPay;
use App\Logic\Shopping\Common\Exception\PaymentOrderStatusException;
use App\Logic\Shopping\Common\Exception\PaymentWaiting;
use App\Logic\Shopping\Common\Exception\PaymentWaitReTry;
use App\Logic\Shopping\Common\LoadDataLogic;
use App\Logic\Shopping\Common\Order\OrderCommonLogic;
use App\Model\Shopping\Order\OrderCommonModel;
use App\Model\Shopping\Order\OrderNumberOriginCommonModel;
use DdvPhp\DdvUtil\String\Conversion;
use Closure;
use JiaLeo\Laravel\Wechatpay\Wechatpay;
use Throwable;
use \App\Model\Shopping\Order\PaymentWxpayCommonModel;
use App\Logic\Shopping\Common\Wechat\OpenAppWechatCommonLogic;

class PaymentWxpayCommonLogic extends LoadDataLogic
{
    //付款ID 和payment表关联
    protected $orderNumber;
    // 原始订单号
    protected $orderNumberOrigin = '';
    // 设备号
    protected $deviceInfo = '';
    // 用户标志
    protected $openid = '';
    // 是否关注公众账号
    protected $isSubscribe = 0;
    // 交易类型
    protected $tradeType = '';
    // 交易状态
    protected $tradeState = '';
    // 标价金额，单位为分
    protected $totalFee = '0';
    // 标价币种
    protected $feeType = 'CNY';
    // 现金支付金额
    protected $cashFee = '0';
    // 现金支付币种
    protected $cashFeeType = '';
    // 微信支付订单号
    protected $transactionId = '';
    // 商户订单号
    protected $outTradeNo = '';
    // 随机字符串
    protected $nonceStr = '';
    // 交易状态描述
    protected $tradeStateDesc = '';
    // 支付完成时间
    protected $timeEnd = '';
    // 付款银行
    protected $bankType = '';
    // 公众账号ID
    protected $appid= '';
    // 子商户公众账号ID
    protected $subAppid = '';
    // 商户号
    protected $mchId = '';
    // 子商户号
    protected $subMchId = '';
    //
    protected $attach = '';
    //
    protected $paymentType = '';

    const NOTIFY_URL_PREFIX = 'http://api.sicmouse.com/v1.0/open/notify';
    const NOTIFY_URL_PATH_WECHAT = '/payment/';

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        if (!empty($this->orderNumber)){
            $model = (new PaymentWxpayCommonModel())->where(['order_number' => $this->orderNumber])->firstHump();
        }elseif (!empty($this->orderNumberOrigin)){
            $model = (new PaymentWxpayCommonModel())->where(['order_number_origin' => $this->orderNumberOrigin])->firstHump();
        }
        if(empty($model)){
            throw new Exception('没有该支付单号', 'ORDER_PAYMENT_NOT_FIND');
        }
        return $model;
    }

    /**
     * @param array $data
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     *
     */
    public function update($data = []){
        // 如果传入了数据就直接加载到逻辑层
        if (!empty($data)){
            $this->load($data);
        }
        try {
            // 试图获取这条数据
            $model = $this->getOne();
        }catch (Exception $e){
            // 不存在就新增
            $model = new PaymentWxpayCommonModel();
        }
        // 读取数据
        $data = $this->getAttributes(null, ['', null]);
        // 设置最新数据到模型
        $model->setDataByArray($data);
        // 数据存回模型
        $this->load($model->toHumpArray());
        // 转下滑杠
        $model->toUnderline();
        // 试图保存数据
        if(!$model->save()){
            throw new Exception('操作失败', 'SAVE_FAIL');
        }
        return true;
    }

    /**
     * @param OrderCommonLogic $orderLogic
     * @param $authCode
     * @return mixed
     * @throws Exception
     * @throws PaymentNext
     * @throws PaymentWaitReTry
     * @throws PaymentWaiting
     * @throws Throwable
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     * @throws \ReflectionException
     */
    public function scanPay(OrderCommonLogic $orderLogic, $authCode){
        $res = [];
        $orderData = $orderLogic->getAttributes(['orderAmount', 'orderNumber', 'orderNumberOrigin', 'orderSubject', 'siteId', 'paymentType']);
        $this->paymentType = $orderData['paymentType'];
        $this->totalFee = floatval($orderData['orderAmount'])*100;
        $this->outTradeNo = $orderData['orderNumber'];
        $this->orderNumber = $orderData['orderNumber'];
        $this->orderNumberOrigin = $orderData['orderNumberOrigin'];
        $this->body = $orderData['orderSubject'];
        $this->tradeType = OrderCommonModel::WECHAT_TRADE_TYPE_MICROPAY;

        $openAppWechatLogic = new OpenAppWechatCommonLogic();

        //得到一个支付的对象

        $app = $openAppWechatLogic->getPaymentAppBySiteId($orderData['siteId']);
        $this->appid = $openAppWechatLogic->getAppId();





        // 获取属性数据
        $sendData = $this->getAttributes(['outTradeNo', 'totalFee', 'appid', 'subAppid', 'mchId', 'subMchId'], ['', null]);
        // 获取发送数据
        $sendData = array_merge($sendData, [
            'auth_code'     =>$authCode,
            'body' => $orderData['orderSubject'],
            // 'notifyUrl' => self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_WECHAT . $queryMethod . '/' . $this->orderNumber
        ]);

        //获取openid
        $openidRes = $app->authCodeToOpenid($authCode);

        try{
            $this->checkResult($openidRes);
        }catch (PaymentWaitReTry $e){
            // 等待再试
            throw $e;
        }

        $this->openid = $openidRes['openid'];
        //支付
        $result = $app->pay($sendData);

        try{
            $this->checkResult($result);
        }catch (PaymentWaitReTry $e){
            //系统错误,等待再试
            throw $e;
        }catch (PaymentOrderStatusException $e){
            // 订单异常，包括：订单已撤销、订单已关闭、订单已支付、商户订单号重复、银行系统异常.银行端超时
            try{
                // 判断是否支付成功
                $orderLogic->checkPaymentSuccess();
            }catch (PaymentNotPay $e){
                // 没有支付
                // 生成新订单,重新支付
                $orderLogic->reCreateOrderNumber();
                // 重新支付
                return $this->scanPay($orderLogic, $authCode);
            }catch (\Throwable $e){
                // 其他系统错误
                throw $e;
            }
        }

        // 转换为驼峰
        $this->load(Conversion::underlineToHumpByArray($result));
        $this->update();

    }

    /**
     * @param $result
     * @throws Exception
     * @throws PaymentNext
     * @throws PaymentOrderStatusException
     * @throws PaymentWaitReTry
     * @throws PaymentWaiting
     */
    public function checkResult($result){

        if(empty($result['return_code'])||$result['return_code']!=='SUCCESS'){
            throw new Exception('微信错误,'.$result['return_msg'],'WECHAT_ERROR');
        }
        if(empty($result['result_code'])||$result['result_code']!=='SUCCESS'){
            switch ($result['err_code']){
                // 系统错误
                case 'SYSTEMERROR':
                    throw new PaymentWaitReTry('系统繁忙,'.$result['err_code_des'],'WECHAT_SYSTEME_ERROR');
                case 'AUTHCODEEXPIRE': // 二维码已过期，请用户在微信上刷新后再试
                    throw new Exception('授权码过期:'.$result['err_code_des'],'WECHAT_AUTHCODEEXPIRE_ERROR');
                case 'NOTENOUGH': // 余额不足
                    throw new Exception('余额不足'.$result['err_code_des'],'WECHAT_SYSTEME_ERROR');
                case 'NOTSUPORTCARD': // 不支持卡类型
                    throw new Exception('该卡不支持当前支付,'.$result['err_code_des'],'WECHAT_NOTSUPORTCARD');
                // 系统错误
                case 'USERPAYING':
                    throw new PaymentWaiting('用户正在输入密码');
                case 'AUTH_CODE_ERROR':
                    throw new Exception('授权码错误:'.$result['err_code_des'],'WECHAT_AUTHCODEEXPIRE_ERROR');
                case 'AUTH_CODE_INVALID':
                    throw new PaymentNext('非微信码','PAY_FAIL');
                case 'BUYER_MISMATCH':
                    throw new Exception('该订单在微信系统中异常，请重试或者重新下单再试','PAY_FAIL');
                case 'PARAM_ERROR': // 参数错误
                case 'NOAUTH': // 商户无权限
                case 'XML_FORMAT_ERROR': // XML格式错误
                case 'REQUIRE_POST_METHOD': // 请使用post方法
                case 'SIGNERROR': // 签名错误
                case 'LACK_PARAMS': // 缺少参数
                case 'NOT_UTF8': // 缺少参数
                case 'APPID_NOT_EXIST': // APPID不存在
                case 'MCHID_NOT_EXIST': // MCHID不存在
                case 'APPID_MCHID_NOT_MATCH': // MCHID不存在
                case 'INVALID_REQUEST': // 无效请求
                case 'TRADE_ERROR': // 交易错误
                    throw new Exception('系统内部异常，请联系客服或程序员处理，'.$result['err_code_des'],'WECHAT_ERROR');
                case 'ORDERREVERSED': // 订单已撤销
                case 'ORDERCLOSED': // 订单已关闭
                case 'ORDERPAID': // 订单已支付
                case 'OUT_TRADE_NO_USED': // 商户订单号重复
                case 'BANKERROR': // 银行系统异常.银行端超时
                    throw new PaymentOrderStatusException('订单异常'.$result['err_code_des']);
                default:
                    throw new Exception($result['err_code_des'],'WECHAT_ERROR');
            }
        }
    }

    /**
     * @param OrderCommonLogic $orderLogic
     * @param null $openId
     * @return array
     * @throws Exception
     * @throws PaymentNext
     * @throws PaymentWaitReTry
     * @throws PaymentWaiting
     * @throws Throwable
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \ReflectionException
     */
    public function unified(OrderCommonLogic $orderLogic, $openId = null){
        $res = [];
        $orderData = $orderLogic->getAttributes(['orderAmount', 'orderNumber', 'orderNumberOrigin', 'orderSubject', 'siteId', 'paymentType']);
        $this->paymentType = $orderData['paymentType']; //支付类型
        $this->totalFee = floatval($orderData['orderAmount'])*100; //支付总e
        $this->outTradeNo = $orderData['orderNumber']; //传给微信端的订单号
        $this->orderNumber = $orderData['orderNumber'];  //订单号
        $this->orderNumberOrigin = $orderData['orderNumberOrigin']; //原始订单号
        $this->body = $orderData['orderSubject']; //商品描述
        $this->tradeType = OrderCommonModel::PATMENT_TYPE_TO_WECHAT_TRADE_TYPE[$this->paymentType];


        $openAppWechatLogic = new OpenAppWechatCommonLogic();
        $app = $openAppWechatLogic->getPaymentAppBySiteId();
//        $this->appid = $openAppWechatLogic->getAppId();
        //得到支付方法
        $queryMethod = $orderLogic->paymentTypeToMethodQuery($this->paymentType);
        // 获取属性数据
        $sendData = $this->getAttributes(['outTradeNo', 'totalFee', 'tradeType', 'appid', 'subAppid', 'mchId', 'subMchId'], ['', null]);
        // 获取发送数据
        $sendData = array_merge($sendData, [
            'body' => !empty($orderData['orderSubject']) ? $orderData['orderSubject'] :  '微信支付',
            'notifyUrl' => self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_WECHAT . $queryMethod . '/' . $this->orderNumber
        ]);
        if (!empty($openId)){
            $this->openid = $openId;
        }else if($this->paymentType!==OrderCommonModel::PATMENT_TYPE_WECHAT_JSAPI){
            throw new Exception('Open Id 不能为空', 'NOT_OAUTH_LOGIN');
        }
        $this->update();
        $sendData['totalFee'] = 0.1;
        $result = $app->order->unify(Conversion::humpToUnderlineByArray($sendData));
        try{
            //判读支付结果
            $this->checkResult($result);
        }catch (PaymentWaitReTry $e){
            //系统错误,请用相同参数重新调用 (重新下单)
            return $this->unified($orderLogic, $openId);
        }catch (PaymentOrderStatusException $e){
            // 订单异常，包括：订单已撤销、订单已关闭、订单已支付、商户订单号重复、银行系统异常.银行端超时
            try{
                // 判断是否支付成功
                $orderLogic->checkPaymentSuccess();
            }catch (PaymentNotPay $e){
                // 没有支付
                // 生成新订单,重新支付
                $orderLogic->reCreateOrderNumber();
                // 重新支付
                return $this->unified($orderLogic, $openId);
            }catch (\Throwable $e){
                // 其他系统错误
                throw $e;
            }
        }

        if(!array_key_exists("appid", $result)|| !array_key_exists("prepay_id", $result)|| $result['prepay_id'] == ""){
            throw new Exception("参数错误",'PARAMS_ERROR');
        }

        $jsApiParameters = $app->jssdk->sdkConfig($result['prepay_id']);

        if (empty($jsApiParameters)) {
            throw new Exception('微信接口对接失败','WECHAT_ERROR');
        }
        $res = [
            'wechatJsSdkConfig'=>$jsApiParameters
        ];

        return $res;
    }

    /**
     * 监听回调
     * @param OrderCommonLogic $orderLogic
     * @param Closure $checkCallback
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Exception
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @throws \ReflectionException
     */
    public function paymentHandle($orderLogic, Closure $checkCallback)
    {
        //获取订单号
        $this->orderNumber = $orderLogic->getOrderNumber();

        // 获取模型 (得到微信支付模型里面的信息)
        $paymentWxpayCommonModel = $this->getOne();
        //获取数据
        $openAppWechatLogic = new OpenAppWechatCommonLogic([
            'wechatAppId'=>$paymentWxpayCommonModel->appid
        ]);
        //得到整个支付的对象
        $app = $openAppWechatLogic->getPaymentApp();

        /**
         * 对自己的业务进行处理并向微信服务器发送一个响应
         * $message 为微信推送推送过来的信息
         */
        $response = $app->handlePaidNotify(function ($message, $fail) use ($checkCallback) {
            try{
                // 转换为驼峰
                $this->load(Conversion::underlineToHumpByArray($message));
                //将微信传过来的信息保存到数据库，有就更新,不存在的话，就添加
                $this->update();
                //处理我们这边的逻辑
                $checkCallback($message);
                // 你的逻辑
                return true;
            }catch(\Throwable $e) {
                $fail($e->getMessage());
            }
        });
        return $response;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws Exception
     * @throws \App\Model\Exception
     * 获取整个原始订单的列表
     */
    public function getListsByOrderNumberOrigin(){
        //查询得到原始订单号
        if (empty($this->orderNumberOrigin)&&(!empty($this->orderNumber))){
            $this->orderNumberOrigin = (new OrderNumberOriginCommonModel())->getOrderNumberOrigin($this->orderNumber);
        }
        //获取下单的详细信息
        $collection = (new PaymentWxpayCommonModel())->where(['order_number_origin' => $this->orderNumberOrigin])->getHump();
        $collection = empty($collection)?[]:$collection;
        return $collection;
    }

    /**
     * @param OrderCommonLogic|null $orderLogic
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \ReflectionException
     * 检查有没有支付成功,检查是否已经支付成功
     */
    public function checkPaymentSuccess(OrderCommonLogic $orderLogic = null){
        //(获取原始订单号)
        $this->orderNumberOrigin = $orderLogic->getOrderNumberOrigin();
        //得到付款列表（存在多个）
        $collection = $this->getListsByOrderNumberOrigin();  //取出当前订单号对应的数据
        $res = null;
        foreach ($collection as $model){
            try{
                $res = $this->checkPaymentSuccessByModel($model);
            }catch (Throwable $e){
                // 没有成功
                if (env('APP_DEBUG')){
                    \Log::info('仅仅调试期间使用打印 异常 - wxpay - checkPaymentSuccess');
                    \Log::info($e->getMessage());
                    \Log::info($e->getLine());
                    \Log::info($e->getFile());
                }
            }
        }
        if(empty($res)){
            throw new Exception('没有找到成功的支付结果', 'PAY_SUCCESS_NOT_FINT');
        }
        return $res;
    }

    /**
     * @param PaymentWxpayCommonModel $model
     * @throws Exception
     * @throws \App\Logic\V2\Pay\NextException
     * @throws \App\Model\Exception
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \ReflectionException
     * 回调逻辑也调用这个方法
     * 下订单和支付回调都是调用这个方法的
     * 先去微信服务器或者支付宝服务器那里调用查询订单，判断订单状态
     * 如果是查询的是非支付成功的状态的话,直接抛出异常给外部程序捕获，这样的话，表明是下单操作，否则的话是支付回调操作
     */
    private function checkPaymentSuccessByModel(PaymentWxpayCommonModel $model){
        //初始化对象
        $openAppWechatLogic = new OpenAppWechatCommonLogic([
            'wechatAppId'=>$model->appid
        ]);
        //得到配置信息
        $app = $openAppWechatLogic->getPaymentApp();
        //调用微信服务器查询订单------判断订单的状态 ??
        $result = $app->order->queryByOutTradeNumber($model->orderNumber);
        if (empty($result['return_code']) || $result['return_code'] !== 'SUCCESS'){
            throw new Exception('微信错误,' . $result['return_code'], 'WECHAT_ERROR');
        }
        if (empty($result['result_code']) || $result['result_code'] !== 'SUCCESS'){
            throw new Exception('微信错误,' . $result['result_code'], 'WECHAT_ERROR');
        }
        if (empty($result['trade_state']) || $result['trade_state'] !== 'SUCCESS'){
            //非支付成功
            throw new Exception('订单非成功状态', 'TRADE_NOT_SUCCESS');
        }
        /**
         * 程序到这里的话,说明订单已经是成功的了
         */
        // 转换为驼峰
        $this->load(Conversion::underlineToHumpByArray($result));
        //保存支付回调的信息
        $this->update();
        if (!empty($result['time_end'])){
            $paymentAt = strtotime($result['time_end']. ' -8 hours');
        }
        return [
            'paymentType'=>$this->paymentType,
            'paymentState'=>OrderCommonModel::PATMENT_STATE_SUCCESS,
            'paymentAt'=>$paymentAt,
        ];
    }
}

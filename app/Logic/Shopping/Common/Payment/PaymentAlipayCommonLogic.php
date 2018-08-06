<?php
namespace App\Logic\Shopping\Common\Payment;

use App\Logic\Exception;

use App\Logic\Shopping\Common\LoadDataLogic;
use DdvPhp\DdvUtil\String\Conversion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Closure;
use Throwable;

class PaymentAlipayCommonLogic extends LoadDataLogic
{
    //付款ID 和payment表关联
    protected $orderNumber;
    // 原始订单号
    protected $orderNumberOrigin = '';
    // 支付宝交易号
    protected $tradeNo = '';
    // 商户订单号
    protected $outTradeNo = '';
    // 买家支付宝账号
    protected $buyerLogonId = '';
    // 交易状态
    protected $tradeStatus = '';
    // 交易订单金额，单位为元
    protected $totalAmount = '';
    // 实收金额，单位为元
    protected $receiptAmount = '';
    // 买家实付金额，单位为元
    protected $buyerPayAmount = '';
    // 积分支付的金额，单位为元
    protected $pointAmount = '';
    // 交易中用户支付的可开具发票的金额
    protected $invoiceAmount = '';
    // 交易支付使用的资金渠道 转为json格式再保存
    protected $fundBillList = '';
    // 买家在支付宝的用户id
    protected $buyerUserId = '';
    // 买家在支付宝的用户id
    protected $buyerId = '';
    // 买家用户类型。CORPORATE:企业用户；PRIVATE:个人用户。
    protected $buyerUserType = '';
    // 订单标题
    protected $subject = '';
    // 对交易或商品的描述
    protected $body = '';
    // 授权商户的appid
    protected $authAppId = '';
    // 卖家支付宝用户ID
    protected $sellerId = '';
    //
    protected $paymentType = '';

    const NOTIFY_URL_PREFIX = 'https://api.sicmouse.com/v1.0/open/notify';
    const NOTIFY_URL_PATH_ALIPAY = '/payment/';

    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        if (!empty($this->orderNumber)){
            $model = (new PaymentAlipayCommonModel())->where(['order_number' => $this->orderNumber])->firstHump();
        }elseif (!empty($this->orderNumberOrigin)){
            $model = (new PaymentAlipayCommonModel())->where(['order_number_origin' => $this->orderNumberOrigin])->firstHump();
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
            $model = new PaymentAlipayCommonModel();
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
     * @return bool
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function scanPay(OrderCommonLogic $orderLogic, $authCode){
        $orderData = $orderLogic->getAttributes(['orderAmount', 'orderNumber', 'orderNumberOrigin', 'orderSubject', 'siteId', 'paymentType', 'orderDiscountAmount']);
        $this->paymentType = $orderData['paymentType'];
        $this->totalAmount = $orderData['orderAmount'];
        $this->outTradeNo = $orderData['orderNumber'];
        $this->orderNumber = $orderData['orderNumber'];
        $this->orderNumberOrigin = $orderData['orderNumberOrigin'];
        $this->subject = $orderData['orderSubject'];
        $this->body = $orderData['orderSubject'].'订单付款';

        // 必须实例化后才可以使用支付宝才请求类
        $openAppAlipayLogic = new OpenAppAlipayCommonLogic();
        $openAppAlipayLogic->getAlipayAppIdBySiteId($orderData['siteId']);

        $queryMethod = $orderLogic->paymentTypeToMethodQuery(OrderCommonModel::PATMENT_TYPE_ALIPAY_MICROPAY);

        // 获取属性数据
        $bizContentArray = $this->getAttributes(['outTradeNo', 'totalAmount', 'subject', 'body'], ['', null]);

        $bizContentArray = Conversion::humpToUnderlineByArray($bizContentArray);

        //往payment表添加一条数据
        $bizContentArray = array_merge($bizContentArray, [
            'scene'        => "bar_code",
            'auth_code'    => $authCode,
            'product_code' => 'FACE_TO_FACE_PAYMENT',
            'discountable_amount' => $orderData['orderDiscountAmount'],
            'operator_id'  => 'yx_001',
            'store_id'     => 'NJ_T_001',
            'terminal_id'  => 'NJ_T_001',
            'extend_params'=> [
                // 'sys_service_provider_id' => '2018010801687754',
                'timeout_express'         => '90m',
            ]
        ]);

        $this->update();

        $request = new \AlipayTradePayRequest ();
        $request->setBizContent ( json_encode($bizContentArray, JSON_UNESCAPED_UNICODE) );
        $request->setNotifyUrl(self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_ALIPAY . $queryMethod . '/' . $this->orderNumber);

        $result = $openAppAlipayLogic->executeAsAuthToken($request);
        if(empty($result->code) || $result->code !== '10000'){
            throw new Exception($result->sub_msg, 'TRADE_CREATE_FAIL');
        }

        $resultArray = json_decode( json_encode( $result, JSON_UNESCAPED_UNICODE),true);

        \Log::info('scanPay');
        \Log::info($resultArray);
        return true;
    }
    /**
     * @param $userId
     * @param OrderCommonLogic $orderLogic
     * @param $orderModel
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function unifiedJsapi(OrderCommonLogic $orderLogic, $userId = null)
    {
        $res = [];

        $this->buyerId = $userId;

        $openAppAlipayLogic = $this->getOpenAppAlipayLogic($orderLogic);

        $queryMethod = $orderLogic->paymentTypeToMethodQuery($this->paymentType);


        // 获取属性数据
        $bizContentArray = $this->getAttributes(['outTradeNo', 'totalAmount', 'subject', 'body', 'buyerId'], ['', null]);

        $bizContentArray = Conversion::humpToUnderlineByArray($bizContentArray);

        $this->update();

        $request = new \AlipayTradeCreateRequest();
        $request->setBizContent ( json_encode($bizContentArray, JSON_UNESCAPED_UNICODE) );
        $request->setNotifyUrl(self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_ALIPAY . $queryMethod . '/' . $this->orderNumber);


        $result = $openAppAlipayLogic->executeAsAuthToken($request);
        if(empty($result->code) || $result->code !== '10000'){
            throw new Exception($result->sub_msg, 'TRADE_CREATE_FAIL');
        }

        $res['alipayConfig'] = Conversion::underlineToHumpByArray((array)$result);

        return $res;
    }

    /**
     * 支付宝电脑网站支付
     * @param OrderCommonLogic $orderLogic
     * @param $returnUrl
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function unifiedWebPage(OrderCommonLogic $orderLogic, $returnUrl){
        //AlipayTradePagePayRequest
        $res = [];

        $openAppAlipayLogic = $this->getOpenAppAlipayLogic($orderLogic);

        $queryMethod = $orderLogic->paymentTypeToMethodQuery($this->paymentType);


        // 获取属性数据
        $bizContentArray = $this->getAttributes(['outTradeNo', 'totalAmount', 'subject', 'body'], ['', null]);

        $bizContentArray = array_merge($bizContentArray, [
            'productCode'=>'FAST_INSTANT_TRADE_PAY'
        ]);

        $bizContentArray = Conversion::humpToUnderlineByArray($bizContentArray);

        $this->update();

        $request = new \AlipayTradePagePayRequest();
        $request->setBizContent ( json_encode($bizContentArray, JSON_UNESCAPED_UNICODE) );
        $request->setReturnUrl($returnUrl);
        $request->setNotifyUrl(self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_ALIPAY . $queryMethod . '/' . $this->orderNumber);

        $res['alipayFormStr'] =  $openAppAlipayLogic->pageExecuteAsAuthToken($request);

        return $res;
    }

    /**
     * 支付宝手机网站支付
     * @param OrderCommonLogic $orderLogic
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    public function unifiedWapPage(OrderCommonLogic $orderLogic, $returnUrl){
        $res = [];

        $openAppAlipayLogic = $this->getOpenAppAlipayLogic($orderLogic);

        $queryMethod = $orderLogic->paymentTypeToMethodQuery($this->paymentType);


        // 获取属性数据
        $bizContentArray = $this->getAttributes(['outTradeNo', 'totalAmount', 'subject', 'body'], ['', null]);

        $bizContentArray = array_merge($bizContentArray, [
            'productCode'=>'QUICK_WAP_PAY'
        ]);

        $bizContentArray = Conversion::humpToUnderlineByArray($bizContentArray);

        $this->update();

        $request = new \AlipayTradeWapPayRequest();
        $request->setBizContent ( json_encode($bizContentArray, JSON_UNESCAPED_UNICODE) );
        $request->setReturnUrl($returnUrl);
        $request->setNotifyUrl(self::NOTIFY_URL_PREFIX . self::NOTIFY_URL_PATH_ALIPAY . $queryMethod . '/' . $this->orderNumber);


        $res['alipayFormStr'] =  $openAppAlipayLogic->pageExecuteAsAuthToken($request);

        return $res;
    }

    /**
     * @param OrderCommonLogic $orderLogic
     * @return OpenAppAlipayCommonLogic
     * @throws Exception
     * @throws \ReflectionException
     */
    protected function getOpenAppAlipayLogic(OrderCommonLogic $orderLogic){

        $orderData = $orderLogic->getAttributes(['orderAmount', 'orderNumber', 'orderNumberOrigin', 'orderSubject', 'siteId', 'paymentType']);
        $this->paymentType = $orderData['paymentType'];
        $this->totalAmount = $orderData['orderAmount'];
        $this->outTradeNo = $orderData['orderNumber'];
        $this->orderNumber = $orderData['orderNumber'];
        $this->orderNumberOrigin = $orderData['orderNumberOrigin'];
        $this->subject = $orderData['orderSubject'];
        $this->body = $orderData['orderSubject'].'订单付款';

        if (empty($this->buyerId)&&$this->paymentType===OrderCommonModel::PATMENT_TYPE_ALIPAY_JSAPI){
            throw new Exception('buyer Id 不能为空', 'BUYER_ID');
        }
        // 必须实例化后才可以使用支付宝才请求类
        $openAppAlipayLogic = new OpenAppAlipayCommonLogic();
        $openAppAlipayLogic->getAlipayAppIdBySiteId($orderData['siteId']);
        return $openAppAlipayLogic;
    }

    /**
     * 监听回调
     * @param OrderCommonLogic $orderLogic
     * @param Closure $checkCallback
     * @param Request|null $request
     * @return Response
     * @throws Exception
     * @throws \ReflectionException
     */
    public function paymentHandle($orderLogic, Closure $checkCallback, Request $request)
    {
        /**
         * 获取订单编号
         */
        $this->orderNumber = $orderLogic->getOrderNumber();
        // 获取模型
        $this->paymentAlipayCommonModel = $this->getOne();
        /**
         * 请求参数为空的话
         */
        if(empty($request->input())){
            throw new Exception('请求错误', 'NOT_FIND_REQUEST');
        }
        // 实例化返回对象
        $response = new Response();
        $response->header('Content-type', 'text/html; charset=gbk', true);

        try {
            $openAppAlipayLogic = new OpenAppAlipayCommonLogic();
            $alipayService = $openAppAlipayLogic->getAlipayTradeService();
            if(!$alipayService->check($_POST)) {
                throw new Exception('验证失败', 'ALIPAY_SERVICE_CHECK_FAIL');
            }
            // 转换为驼峰
            $this->load(Conversion::underlineToHumpByArray($request->input()));
            /**
             * 插入或者更新payment_alipay表
             */
            $this->update();
            //检查orderCommonLoigc表里面的方法
            $checkCallback($request->input());
        } catch (Exception $e) {
            //验证失败-返回支付宝
            $response->setContent('fail');
        }


        $response->setContent('success');//请不要修改或删除
        return $response;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws Exception
     * @throws \App\Model\Exception
     */
    public function getListsByOrderNumberOrigin(){
        if (empty($this->orderNumberOrigin)&&(!empty($this->orderNumber))){
            $this->orderNumberOrigin = (new OrderNumberOriginCommonModel())->getOrderNumberOrigin($this->orderNumber);
        }
        $collection = (new PaymentAlipayCommonModel())->where(['order_number_origin' => $this->orderNumberOrigin])->getHump();
        $collection = empty($collection)?[]:$collection;
        return $collection;
    }

    /**
     * @param OrderCommonLogic|null $orderLogic
     * @return null
     * @throws Exception
     * @throws \App\Model\Exception
     * @throws \ReflectionException
     */
    public function checkPaymentSuccess(OrderCommonLogic $orderLogic = null){
        //获取订单数据
        $this->orderNumberOrigin = $orderLogic->getOrderNumberOrigin();
        //通过原始订单号查询下面的订单信息(查询对应的订单列表，存在多个)
        $collection = $this->getListsByOrderNumberOrigin();
        $res = null;
        foreach ($collection as $model){
            try{
                /**
                 * 支付宝支付
                 */
                $res = $this->checkPaymentSuccessByModel($model);
            }catch (Throwable $e){
                // 没有成功
                if (env('APP_DEBUG')){
                    \Log::info('仅仅调试期间使用打印 异常 - alipay - checkPaymentSuccess');
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
     * @param PaymentAlipayCommonModel $model
     * @return array
     * @throws Exception
     * @throws \DdvPhp\Alipay\Exception
     * @throws \ReflectionException
     */
    private function checkPaymentSuccessByModel(PaymentAlipayCommonModel $model){
        /**
         * 初始化得到一个支付宝对象
         */
        $openAppAlipayLogic = new OpenAppAlipayCommonLogic([
            'alipayAppId' => $model->authAppId
        ]);
        //订单交易号
        $bizContentArray = [
            'out_trade_no' => $model->orderNumber,
        ];
        $request = new \AlipayTradeQueryRequest();
        //设置交易单号
        $request->setBizContent ( json_encode($bizContentArray, JSON_UNESCAPED_UNICODE) );
        //获得请求对象
        $resultObj = $openAppAlipayLogic->executeAsAuthToken( $request);
        if (is_object($resultObj)){
            $resultArray = json_decode( json_encode( $resultObj, JSON_UNESCAPED_UNICODE),true);
            if (isset($resultArray)&&isset($resultArray['fund_bill_list'])&&is_array($resultArray['fund_bill_list'])){
                $resultArray['fund_bill_list'] = json_encode($resultArray['fund_bill_list'], JSON_UNESCAPED_UNICODE);
            }
        }else{
            $resultArray = [];
        }
        $resultCode = $resultObj->code;

        if(empty($resultCode) || $resultCode !== '10000'){
            throw new Exception( $resultArray['msg'], 'CHECK_ERROR');
        }
        if ($resultArray['trade_status'] != 'TRADE_SUCCESS'){
            //非交易
            throw new Exception('订单非成功状态', 'TRADE_NOT_SUCCESS');
        }
        /**
         * 程序来到这里的话,表示已经支持成功了。
         */
        !empty($resultArray['fund_bill_list']) ? $resultArray['fund_bill_list'] =  json_encode($resultArray['fund_bill_list']) : '';
        // 转换为驼峰
        $this->load(Conversion::underlineToHumpByArray($resultArray));
        $this->update();

        return [
            'paymentType'=>$this->paymentType,
            'paymentState'=>OrderCommonModel::PATMENT_STATE_SUCCESS,
            'paymentAt'  => strtotime($resultArray['send_pay_date'] . '-8 hours')
        ];

    }
}

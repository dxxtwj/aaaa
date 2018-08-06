<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2018/1/31
 * Time: 下午8:33
 */

namespace App\Logic\Shopping\Common\Order;
use App\Logic\Shopping\Common\Exception\PaymentNotPay;
use App\Model\Shopping\Order\OrderCommonModel;
use App\Model\Shopping\Order\OrderModel;
use App\Model\Shopping\Order\OrderNumberOriginCommonModel;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Throwable;
use \App\Logic\Shopping\Common\LoadDataLogic;
use App\Logic\Exception;
class OrderCommonLogic extends LoadDataLogic
{
    // 订单号，全局唯一
    protected $orderNumber = '';
    // 原始订单号
    protected $orderNumberOrigin = '';
    // 订单类型:-换货[-2];refund-退款[-1];online-下单[1];collect-收款[2];recharge-充值[3]
    protected $orderType = 0;
    // 收款状态:-3管理员取消;-2.客户取消;-1.超时关闭;0.未付款[待支付];1.已收款[待发货];2.待收货;3.待评价;4.已评价
    protected $orderState = 0;
    // 订单标题
    protected $orderSubject = '';
    // 实收金额[原始金额-折扣金额]
    protected $orderAmount = 0;
    // 原始金额[总金额]
    protected $orderTotalAmount = 0;
    // 折扣金额
    protected $orderDiscountAmount = 0;
    // 订单授权guid
    protected $orderAuthGuid = '';

    // 支付方式[平台+方式]: [1:支付宝;2:微信;3:QQ;4:微博;]+[0:统一;1:wap;2:pc;3:公众号;4:app;5:扫码];比如微信扫码是25
    protected $paymentType = '';
    protected $paymentTypesTry = '';
    // 付款状态：-1 超时，0 未付款，1 付款成功， 2 退款一部分，3 全部退款
    protected $paymentState = '';
    // 已经退款金额
    protected $paymentRefund = '';
    // 支付时间
    protected $paymentAt = null;

    // 订单支付uid
    protected $paymentUid = 0;
    // 收款者uid
    protected $collectUid = 0;
    // 订单创建者uid
    protected $createdUid = 0;
    // 订单拥有者uid
    protected $ownerUid = 0;
    // 站点ID
    protected $siteId = '';
    // 备注
    protected $remarks = '';
    //物流状态 默认未付款
    protected $deliveryState = -1;
    /**
     * @var OrderCommonModel $orderModel
     */
    public $orderModel = null;

    public $insideRemarks = '';

    public function getOrderNumber(){
        return $this->orderNumber;
    }

    /**
     * @return string
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getOrderNumberOrigin(){
        if (empty($this->orderNumberOrigin)){
            $this->load($this->getOne()->toHumpArray());
        }
        return $this->orderNumberOrigin;
    }
    /**
     * @return \DdvPhp\DdvUtil\Laravel\Model|null
     * @throws Exception
     */
    public function getOrderModel(){
        $orderModel = (new OrderCommonModel())->whereOrderNumber($this->orderNumber)->firstHump();
        if(empty($orderModel)){
            throw new RJsonError('订单不存在', 'NOT_FIND_COLLECT');
        }
        return $orderModel;
    }
// *********************************************      订单详情开始       *************************************************
    /**
     * 前后端在线下单订单详情
     * 共用部分一
     * @return OrderCommonModel|\DdvPhp\DdvUtil\Laravel\Model|null
     * @throws Exception
     */
    public function orderShowPart1(){
        $this->orderModel = $this->getOrderModel();
        if ($this->orderModel->orderType === OrderCommonModel::ORDER_TYPE_ONLINE){
            /**
             * 在线下单
             * @var OrderCommonModel $orderModel
             */
            $this->getOrderInfo();
            $this->orderModel->endTime = (($this->orderModel->createdAt + 60*60*24) - time()) < 0 ? 0 : ($this->orderModel->createdAt + 60*60*24) - time();
            $delivery = $this->orderModel->getDeliveryAddress()->firstHump();
            if (!empty($delivery)){
                /**
                 * @var DeliveryCommonModel $delivery
                 */
                $deliveryCompany = $delivery->getCompany()->firstHump(['delivery_name']);
                $delivery->companyName = !empty($deliveryCompany) ? $deliveryCompany->deliveryName : '';
                $delivery->provinceName = AddressController::getRegionName($delivery->provinceRegionId);
                $delivery->cityName = AddressController::getRegionName($delivery->cityRegionId);
                $delivery->address = AddressController::getRegionName($delivery->areaRegionId);
                $this->orderModel->address = $delivery;
            }
        }
        if ($this->orderModel->isInvoice == OrderCommonModel::ORDER_EXIST_INVOICE){
            //开过发票的信息
            $invoice = $this->orderModel->getInvoice()->firstHump(['invoice_id']);
            if(!empty($invoice)){
                $this->orderModel->invoiceInfo = (new InvoiceCommonModel())->where('invoice_id', $invoice->invoiceId)->firstHump(['nature', 'type', 'invoice_title']);
            }
        }
    }

    /**
     * 前后端在线下单订单详情
     * 共用部分二
     */
    public function orderShowPart2(){
        $collectUserInfo = (new UserInfoCommonModel())->where(['uid' => $this->orderModel->createdUid])->select('name')->first();
        if(!empty($collectUserInfo)){
            $this->orderModel->setDataByArray($collectUserInfo->toHumpArray());
        }
        $siteInfo = (new SiteCommonModel())->where(['site_id' => $this->orderModel->siteId])->select('company_name', 'phone', 'payment_logo', 'site_logo')->first();
        if(!empty($siteInfo)){
            $this->orderModel->setDataByArray($siteInfo->toHumpArray());
        }
    }

    /**
     * @param OrderCommonModel $this->orderModel
     */
    public function getOrderInfo(){
        $orderGoods = $this->orderModel->getOrderGoodsLists()->getHump();
        if (!$orderGoods->isEmpty()){
            /**
             * @var OrderGoodsCommonModel $goods
             */
            foreach($orderGoods as $goods){
                $stock = (new GoodsSpaceModel())->where(['goods_id' => $goods->goodsId, 'space_id' => $goods->spaceId])->firstHump(['stock']);
                if (!empty($stock)){
                    $goods->stock = $stock->stock ?? 0;
                }
                $thumb = $goods->getOrderGoodsThumb()->select(['img'])->firstHump();
                $goods->thumb = !empty($thumb) ? $thumb->img : '';
            }
            $this->orderModel->orderGoods = $orderGoods->toArray();
        }
        $orderOwner = $this->orderModel->getOrderOwnerUser()->select('phone as name')->firstHump();
        if (empty($orderOwner)){
            $orderOwner = $this->orderModel->getOrderCompany()->select('company_name as name')->firstHump();
            if (!empty($orderOwner)){
                $orderOwner->setDataByArray(['type' => UserBaseCommonModel::TYPE_TO_COMPANY]);
            }
        }else{
            $orderOwner->setDataByArray(['type' => UserBaseCommonModel::TYPE_TO_USER]);
        }
        $this->orderModel->orderOwner = $orderOwner;
    }
//********************************      订单详情结束      *****************************************************


    /**
     * 创建订单
     * @return array
     * @throws Exception
     * @throws \ReflectionException
     */
    public function create(){
        if (empty($this->orderDiscountAmount)){
            $this->orderDiscountAmount=0;
        }
        $this->orderTotalAmount = $this->orderDiscountAmount + $this->orderAmount;
        // 订单授权guid
        $this->orderAuthGuid = \DdvPhp\DdvAuth\Sign::createGuid();
        // 生成订单号
        if (empty($this->orderNumber)){
            $this->orderNumber = self::addOrderNumber();
        }
        // 保存原始订单号
        $this->orderNumberOrigin = $this->orderNumber;
        if (empty($this->orderSubject)){
            $this->orderSubject = '付款'.$this->orderAmount.'元';
        }
        // 获取逻辑层的数据
        $data = $this->getAttributes(null, ['', null, 0]);
        // 创建订单模型
        $orderModel = new OrderModel();
        // 设置数据到数据模型
        $orderModel->setDataByHumpArray($data);
        // 保存
        if(!$orderModel->save()){
            throw new Exception('创建订单失败','CREATED_ORDER_ERROR');
        }
        $data = [
            'orderNumber' => $this->orderNumber,
            'orderAuthGuid' => $this->orderAuthGuid
        ];
        return $data;
    }


    /**
     * 修改订单
     * @return bool
     * @throws Exception
     * @throws \ReflectionException
     */
    public function update(){
         //得到订单的数据
        $orderModel = $this->getOrderModel()->toHump();
        // 获取逻辑层的数据
        $data = $this->getAttributes(null, ['', null]);
        $orderModel->setDataByArray($data);
        $orderModel->toUnderline();
        if(!$orderModel->save()){
            throw new Exception('修改订单失败','UPDATE_ORDER_ERROR');
        }
        return true;
    }

    /**修改付款方
     * @return bool
     * @throws Exception
     */
    public function modifyPay(){
        $order = (new OrderCommonModel())->whereOrderNumber($this->orderNumber)->where(['order_type' => OrderCommonModel::ORDER_TYPE_COLLECT])->first();
        if(empty($order)){
            throw new Exception('订单不存在', 'ERROR_NOT_FINDORDER');
        }
        $order->owner_uid = $this->ownerUid;
        if($order->save()){
            return true;
        }
        return false;
    }


    /**
     * 生成订单编号
     * @return string
     * @throws Exception
     */
    public static function addOrderNumber(){
        $number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        //生成一个唯一的订单号
        $orderNumber = (new OrderModel())->where(['order_number' => $number])->first();
        if(!empty($orderNumber)){
            return self::addOrderNumber();
        }
        $orderNumberOrigin = (new OrderNumberOriginCommonModel())->where(['order_number' => $number])->first();
        if(!empty($orderNumberOrigin)){
            return self::addOrderNumber();
        }
        if(empty($number)){
            throw new Exception('生成订单编号失败', 'ADD_ORDERNUMBER_ERROR');
        }
        return $number;
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public function reCreateOrderNumber(){
        // 生成订单号
        $this->orderNumber = self::addOrderNumber();
        $this->update();
    }
    /**
     * 获取支付方式
     * @param string $appName
     * @param string $appType
     * @param null $paymentType
     * @return string
     * @throws Exception
     */
    public function getPaymentTypeBy($appName = '' ,$appType = 'mp' ,$paymentType = null)
    {
        if (empty($paymentType)){
            $key = $appName .'_'. $appType;
            if (empty(OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE[$key])){
                throw new Exception("不支持该支付方式[{$appName}-{$appType}]", 'PATMENT_TYPE_NOT_SUPPORTED');
            }
            $paymentType = OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE[$key];
        }
        $paymentType = (int)$paymentType;
        if (!in_array($paymentType, OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE, true)){
            throw new Exception("不支持该支付方式[{$paymentType}]", 'PATMENT_TYPE_NOT_SUPPORTED');
        }
        //
        return $paymentType;
    }
    /**
     * @param null $paymentType
     * @return mixed
     * @throws Exception
     */
    public function paymentTypeToMethodQuery($paymentType = null){
        if (empty($paymentType)){
            $paymentType = $this->paymentType;
        }
        if (empty($paymentType)){
            throw new Exception('支付类型必须输入', 'PATMENT_TYPE_MUST_INPUT');
        }

        if (empty(OrderCommonModel::PATMENT_TYPE_TO_METHOD_QUERY[$paymentType])){
            throw new Exception('不支持该方法', 'PATMENT_TYPE_TO_METHOD_NOT');
        }
        return OrderCommonModel::PATMENT_TYPE_TO_METHOD_QUERY[$paymentType];
    }

    /**
     * 检查订单支付是否成功
     * @param null $paymentModel
     * @throws Exception
     * @throws PaymentNotPay
     * @throws \ReflectionException
     * 1 判断支付状态，支付方式
     * 2 得到该订单的数据
     * 3 获取支付集合 支付方式
     * 4
     */
    public function checkPaymentSuccess(){
        /**
         * 判断该订单的支付状态，如果是支付状态为空,或者是支付状态小于1的话
         * 重新获取数据获取该订单的数据
         * 如果订单状态大于1 的话，直接返回成功了。
         */
        if (empty($this->paymentState) || $this->paymentState < OrderCommonModel::ORDER_STATE_PAY_SUCCESS){
            $this->load($this->getOrderModel()->toHumpArray()); //加载数据模型
        }
        if ($this->paymentState >= OrderCommonModel::ORDER_STATE_PAY_SUCCESS){
            // 如果支付成功的不重做以下判断逻辑，直接中断返回
            return true;
        }
        // 获取支付集合 支付方式 得到支付方式
        /**
         * 取得支付方式，得到之前的支付方式。
         * 循环每一种支付方式进行调用,在对应方法里面的checkPaymentSuccess
         * 进行判断，如果是下单操作的话，直接抛出异常给外部程序捕获
         * 如果是支付回调的话，直接进行下面的操作:
         *
         */
        $checkClassArrays = $this->getCheckClassArrays();
        isset($checkClassArrays)&&\Log::info($checkClassArrays);
        // 定义一个接收结果的变量
        $res = null;
        //加载对应的支付的类进行操作
        foreach($checkClassArrays as $item) {
            $className = $item;
            try{
                 //判断对应支付的类是否存在
                if (class_exists($className)) {
                    $checkLogic = new $className([
                        'orderNumber' => $this->orderNumber
                    ]);
                    //判断支付类的方法是否存在
                    if (method_exists($checkLogic, 'checkPaymentSuccess')){
                        $res = $checkLogic->checkPaymentSuccess($this);
                        break;
                    }
                }
            }catch (Throwable $e){
                if (env('APP_DEBUG')){
                    \Log::info('仅仅调试期间使用打印 异常 - all - checkPaymentSuccess');
                    \Log::info($e->getMessage());
                    \Log::info($e->getLine());
                    \Log::info($e->getFile());
                }
            }
        }
        /**
         * 如果此处判断为空的话,直接抛出异常给外部调用捕获。
         * 如果不为空的话,程序直接往下执行
         */
        if (empty($res)){
            // 没有查到成功支付的结果
            throw new PaymentNotPay('尚未支付', 'ORDER_NOT_PAYMENT');
        }
        \Log::info('*-*');
        \Log::info($res);
        // 获取支付状态
        $this->paymentState = $res['paymentState'];
        $this->paymentAt = $res['paymentAt'];
        // 判断支付状态 (判断微信或者支付宝里面的订单数据状态)---如果是未支付的
        if ($this->paymentState <= OrderCommonModel::PATMENT_STATE_UNPAY){
            // 没有收到成功支付的状态
            throw new PaymentNotPay('尚未支付', 'ORDER_NOT_PAYMENT');
        };
        // 如果已经支付，可是订单数据库中的数据还是没有支付成功的状态才修改数据
        if ($this->orderState<OrderCommonModel::ORDER_STATE_PAY_SUCCESS){
            // 修改订单状态 为支付成功
            $this->orderState = OrderCommonModel::ORDER_STATE_PAY_SUCCESS;
            //修改订单支付方式
            $this->paymentType = $res['paymentType'];
            //修改订单的物流状态
            $this->deliveryState = OrderCommonModel::ORDER_DELIVERY_STATE_WAIT_SHIPPED;
            //更新数据
            $this->update();
            $this->orderEventPaySuccess();
        }

    }

    /**
     * 获取支付集合
     * @return array
     * @throws PaymentNotPay
     */
    private function getCheckClassArrays(){
        //支付方式不存在,表示还没有支付
        if (empty($this->paymentTypesTry)){
            throw new PaymentNotPay('尚未支付', 'ORDER_NOT_PAYMENT');
        }
        //加载以前的支付方式,重新支付
        $res = [];
        foreach (explode(',', $this->paymentTypesTry) as $paymentType){
            if (!empty(OrderCommonModel::PATMENT_TYPE_TO_CHECK_CLASS_NAME[$paymentType])){  //类型 33
                $res[] = OrderCommonModel::PATMENT_TYPE_TO_CHECK_CLASS_NAME[$paymentType];  //加载对应支付的类放进数组里面
            }
        };
        //找不到支付方式，直接抛出错误
        if (empty($res)){
            throw new PaymentNotPay('尚未支付', 'ORDER_NOT_PAYMENT');
        }
        return $res;
    }

    /**
     * 这里一定只能是加入定时任务到定时任务数据库
     * 请不要在这里做长时间的操作逻辑和重量级运算
     */
    private function orderEventPaySuccess(){
        \Log::info('微信-支付宝-通知等');
        //修改物流表该订单状态为待发货
        $deliveryModel = (new DeliveryCommonModel())->where(['type' => DeliveryCommonModel::DELIVERY_TYPE_PHYSICAL_PRODUCTS, 'order_number' => $this->orderNumber])->first();
        if (!empty($deliveryModel)){
            $deliveryModel->state = DeliveryCommonModel::DELIVERY_STATE_PENDING;
            $deliveryModel->save();
        }
        //修改商品销售量
        $orderGoodsModel = (new OrderGoodsCommonModel())->where('order_number_origin', $this->orderNumberOrigin)->firstHump();
        if (!empty($orderGoodsModel)){
            $goodsSpaceModel = $orderGoodsModel->getGoodsSpace()->first();
            if (!empty($goodsSpaceModel)){
                $goodsSpaceModel->total_sales = $goodsSpaceModel->total_sales + $orderGoodsModel->number;
                $goodsSpaceModel->save();
            }
        }
        // 加入定时任务，准备撤销多余的支付
        // 加入定时任务，发送通知给客户、平台客服[销售]、财务对账通知，支付成功
    }
}
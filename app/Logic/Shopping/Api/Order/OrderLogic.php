<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/6/8
 * Time: 下午2:29
 */

namespace App\Logic\Shopping\Api\Order;

use \App\Logic\Shopping\Common\Exception\PaymentNotPay;
use App\Logic\Common\ShoppingLogic;
use App\Logic\Exception;
use App\Logic\Shopping\Common\Exception\PaymentPayed;
use App\Logic\Shopping\Common\Order\OrderCommonLogic;
use App\Logic\Shopping\Common\Payment\PaymentWxpayCommonLogic;
use App\Model\Shopping\Cart\CartModel;
use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\Order\OrderCommonModel;
use App\Model\Shopping\Order\OrderModel;
use App\Model\Shopping\OrderGoods\OrderGoodsModel;
use App\Model\Shopping\Refund\RefundModel;
use App\Model\Shopping\User\UserModel;
use App\Model\Shopping\Discount\DiscountModel;
use App\Model\Shopping\UserDiscount\UserDiscountModel;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use Illuminate\Database\QueryException;

class  OrderLogic extends OrderCommonLogic
{


    /*
     * 添加订单表和订单商品表
     * @param array $data 订单数据 & 订单商品表的数据
     * @return null
     */
    public static function addOrder($data)
    {
        $orderModel = new OrderModel();
        $goodsModel = new GoodsModel();
        $cartModel = new CartModel();
        $discountModel = new DiscountModel();
        $userdiscountModel = new UserDiscountModel();
        $price = array();

        if (!empty($data['cartId'])) {

            $cartData = $cartModel->whereIn('cart_id', $data['cartId'])->getHumpArray();
            if (!empty($cartData)) {

                foreach ($cartData as $k => $v) {
                    $goods = $goodsModel->where('goods_id', $v['goodsId'])->firstHumpArray();
                    $common = new ShoppingLogic();
                    $goodsFirstData = $common->goodsPrice($goods, $v['shopId']);
                    if ($goodsFirstData['goodsShow'] == 0) {//下架

                        $cartModel->where('cart_id', $v['cartId'])->delete();
                        throw new RJsonError($goodsFirstData['goodsName'] . '该商品已经下架，请重新下单', 'ORDER_ERROR');
                    }
                    $price[] = (float)$goodsFirstData['goodsPrice'] * (float)$v['cartNumber'];//单个商品的价格  数量*单价
                }

                \DB::beginTransaction();
                try {
                    $orderRec['order_total_price'] = array_sum($price);//商品的总价
                    $orderRec['user_phone'] = empty($data['userPhone']) ? '' : $data['userPhone'];//用户手机
                    $orderRec['order_contents'] = empty($data['orderContents']) ? '' : $data['orderContents'];//订单描述
                    $orderRec['order_status'] = 1;// 1： 待付款  2： 已付款  3：已完成
                    $orderRec['order_name'] = substr(str_shuffle(time() . mt_rand(10000000, 9999999999)), 0, 15);//订单号
                    $orderRec['shop_id'] = $data['shopId'];
                    $orderRec['shop_name'] = $data['shopName'];
                    $orderRec['user_id'] = \Session::get('userId');//用户ID

                    if (!empty($data['userDiscountId'])) {

                        //判断商品的总价是否已达到优惠金额  判断用户优惠券是否可用
                        $userdiscount = $userdiscountModel
                                    ->where("user_discount_id", $data['userDiscountId'])
                                    ->firstHumpArray();
                        $now = time();
                        if($userdiscount['discountStatus'] == 1 && $now < $userdiscount['stop']){
                            $discount = $discountModel
                                ->where('full_reduced_id', $userdiscount['fullReducedId'])
                                ->firstHumpArray();

                            if (!empty($discount) && $now > $discount['start']){
                                //判断属于那种优惠类型
                                if ($discount['discountType'] == 1){ //满减
                                    if (array_sum($price) >= $discount['amountMoney']){ //达到优惠金额
                                        $floor = floor(array_sum($price)  / $discount['amountMoney']);
                                        $youhui = $floor * $discount['reducedMoney'];   //总优惠
                                        $orderRec['order_total_price'] = array_sum($price) - $youhui;
                                    } else {
                                        $orderRec['order_total_price'] = array_sum($price);
                                    }
                                } elseif ($discount['discountType'] == 2){ //折扣
                                    if (array_sum($price) >= $discount['amountMoney']){ //达到优惠金额
                                        $orderRec['order_total_price'] = array_sum($price) * $discount['zheKou'];
                                    } else {
                                        $orderRec['order_total_price'] = array_sum($price);
                                    }
                                } elseif ($discount['discountType'] == 3){
                                    if (array_sum($price) >= $discount['amountMoney']){ //达到优惠金额
                                        $orderRec['order_total_price'] = array_sum($price) - $discount['voucherMoney'];
                                    } else {
                                        $orderRec['order_total_price'] = array_sum($price);
                                    }

                                }
                            } else {
                                throw new RJsonError('优惠券不在有效期内','DISCOUNT_ERROR');
                            }

                            $orderModel->setDataByHumpArray($orderRec)->save();//添加进订单表
                            $lastId = $orderModel->getQueueableId();//获取id

                            if ($lastId){ //下单成功后修改优惠券为失效  并在优惠券表中添加已使用数量
                                $used['discount_used'] = $discount['discountUsed'] + 1;
                                $discountModel->where('full_reduced_id',$discount['fullReducedId'])->updateByHump($used);
                                $status['discount_status'] = 0;
                                $userdiscountModel->where('user_discount_id',$data['userDiscountId'])->updateByHump($status);
                            } elseif (!$lastId) {
                                throw new RJsonError('下单失败', 'ORDER_ERROR');
                            }
                        }
                    }

                    $orderModel->setDataByHumpArray($orderRec)->save();//添加进订单表
                    $lastId = $orderModel->getQueueableId();//获取id
                    if (!$lastId) {
                        throw new RJsonError('下单失败', 'ORDER_ERROR');
                    }

                    foreach ($cartData as $k => $v) {

                        $goods = $goodsModel->where('goods_id', $v['goodsId'])->firstHumpArray();

                        $common = new ShoppingLogic();
                        $goodsFirstData = $common->goodsPrice($goods, $v['shopId']);
                        $orderGoodsModel = new OrderGoodsModel();

                        $orderGoodsRec['order_id'] = $lastId;//订单id
                        $orderGoodsRec['goods_id'] = $goodsFirstData['goodsId'];//订单商品表的数据
                        $orderGoodsRec['goods_img'] = $goodsFirstData['goodsImg'];//订单商品表的数据
                        $orderGoodsRec['goods_price'] = $goodsFirstData['goodsPrice'];//订单商品表的数据
                        $orderGoodsRec['goods_name'] = $goodsFirstData['goodsName'];//订单商品表的数据
                        $orderGoodsRec['goods_number'] = $v['cartNumber'];//订单商品表的数据

                        $bool = $orderGoodsModel
                            ->setDataByHumpArray($orderGoodsRec)
                            ->save();//添加进订单商品表
                    }

                    if (empty($bool)) {
                        throw new RJsonError('下单失败', 'ORDER_ERROR');
                    }
                    $cartModel->whereIn('cart_id', $data['cartId'])->delete();
                    \DB::commit();
                    return ['data' => array('orderId' => $lastId)];//返回订单ID
                } catch (QueryException $e) {
                    \DB::rollBack();
                    throw new RJsonError($e->getMessage(), 'ORDER_ERROR');
                }
            }
        }
    }

    /*
     * 前台查询订单表
     */
    public static function showOrder($data)
    {

        $orderModel = new OrderModel();
        $orderGoodsModel = new OrderGoodsModel();
        $refundModel = new RefundModel();

        if (empty($data['orderId'])) { // 查所有

            $orderWhere['user_id'] = \Session::get('userId');
            $orderWhere['shop_id'] = $data['shopId'];
            $orderWhere['order_delete'] = 1;
            if (!empty($data['status'])) {
                $orderWhere['order_status'] = $data['status'];
            }
            $orderData = $orderModel
                ->where($orderWhere)
                ->orderBy('order_id', 'DESC')
                ->getDdvPageHumpArray();

            foreach ($orderData['lists'] as $k => $v) {

                $orderData['lists'][$k]['orderGoodsData'] = $orderGoodsModel
                    ->where('order_id', $v['orderId'])
                    ->getHumpArray();

                if ($v['refundStatus'] != 0) {
                    $orderData['lists'][$k]['AfterSale']['refundStatus'] = $v['refundStatus'];
                    $orderData['lists'][$k]['AfterSale']['refundContents'] = $v['refundContents'];
                    $orderData['lists'][$k]['AfterSale']['shopContents'] = $v['shopContents'];
                    $orderData['lists'][$k]['AfterSale']['refundExplain'] = $v['refundExplain'];

                }
            }
            return $orderData;

        } elseif (!empty($data['orderId'])) {//查单条

            $orderData = $orderModel->where('order_id', $data['orderId'])->firstHumpArray();
            $orderData['orderGoodsData'] = $orderGoodsModel->where('order_id',$data['orderId'])->getHumpArray();
            if ($orderData['refundStatus'] != 0) {//有售后

                $orderData['AfterSale']['refundStatus'] = $orderData['refundStatus'];
                $orderData['AfterSale']['refundContents'] = $orderData['refundContents'];
                $orderData['AfterSale']['refundExplain'] = $orderData['refundExplain'];
                $orderData['AfterSale']['shopContents'] = $orderData['shopContents'];
            }
            return ['data' => $orderData];
        }
    }

    /*
     * 前台删除订单
     */
    public static function deleteOrder($orderId)
    {

        $orderModel = new OrderModel();

        $update['order_delete'] = 2;
        $bool = $orderModel->where('order_id', $orderId)->updateByHump($update);

        if (!$bool) {

            throw new RJsonError('删除订单失败', 'ORDER_ERROR');
        }

        return;
    }

    /*
     * 更改订单状态
     */
    public static function editOrder($data)
    {
        $orderModel = new OrderModel();
        $shopId = \Session::get('shopId');

        if (empty($shopId)) {

            throw new RJsonError('商家未登录', 'SHOP_ERROR');
        }

        $orderData = $orderModel->where('order_name', $data['orderName'])->firstHumpArray();

        if (empty($orderData)) {
            throw new RJsonError('暂无该订单的数据', 'ORDER_ERROR');

        }

        if ($orderData['orderStatus'] == 1) {// 未付款
            throw new RJsonError('该订单还未付款', 'ORDER_ERROR');
        }

        if ($orderData['orderStatus'] == 3) {// 已付款
            throw new RJsonError('该订单已完成', 'ORDER_ERROR');
        }

        if ($orderData['orderStatus'] == 2) {// 已付款
            if ($orderData['refundStatus'] == 1) {
                throw new RJsonError('该订单有售后申请操作', 'ORDER_ERROR');
            }
            if ($orderData['refundStatus'] == 2) {
                throw new RJsonError('该订单已经退款了', 'ORDER_ERROR');
            }
            $status['order_status'] = 3;//1： 待付款  2： 已付款  3：已完成
            $bool = $orderModel->where('order_name', $data['orderName'])->updateByHump($status);

            if (!$bool) {
                throw new RJsonError('操作失败', 'ORDER_ERROR');
            }
        }

        return;
    }

    /**
     * @param $inputData
     * 统一下单
     */
    public function unified($inputData)
    {
        try {
            // 开启事物
            \DB::beginTransaction();
            $res = $this->unifiedRun($inputData);
            // 提交事物
            \DB::commit();
            return $res;
        } catch (PaymentPayed $e) {
            // 已经支付需要提交事务
            \DB::commit();
            throw $e;
        } catch (\Throwable $e) {

            // 回滚
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $inputData
     * 订单逻辑
     */
    public function unifiedRun($inputData)
    {
        /**
         * 获取订单数据
         */
        $this->load($this->getOneByAuthCode()->toHumpArray());
        try {
            // 判断是否支付成功
            $this->checkPaymentSuccess();
            // 防止重复支付
            throw new PaymentPayed('已经支付', 'ORDER_PAYMENT_PAYED');
        } catch (\App\Logic\Shopping\Common\Exception\PaymentNotPay $e) {
            if (env('APP_DEBUG')) {
                \Log::info('仅仅调试期间使用打印 异常 - Web\Order\OrderLogic - unifiedRun');
                \Log::info($e->getMessage());
                \Log::info($e->getLine());
                \Log::info($e->getFile());

            }
        }
        //试图获取支付类型。
        //程序执行到这里表明是还没有支付成功
        $this->paymentType = $this->getPaymentTypedBy($inputData['appName'], $inputData['appType'], $inputData['paymentType']);
        $paymentTypesTryArray = empty($this->paymentTypesTry) ? [] : explode(',', $this->paymentTypesTry);
        if (!in_array($this->paymentType, $paymentTypesTryArray)) {
            array_unshift($paymentTypesTryArray, $this->paymentType);
        }
        //组合曾经的支付方式
        $this->paymentTypesTry = implode(',', $paymentTypesTryArray);
        //将以前的支付方式更新到数据库中

        $this->update();

        if (empty(OrderCommonModel::PATMENT_TYPE_TO_METHOD_UNIFIED[$this->paymentType])) {
            throw new Exception('不支持该方法', 'PAYMENT_TYPE_TO_METHOD_UNIFIED_NOT');
        }
        $method = 'unified' . OrderCommonModel::PATMENT_TYPE_TO_METHOD_UNIFIED[$this->paymentType];
        if (!method_exists($this, $method)) {
            throw new Exception('暂不支持该支付类型', 'METHOD_NOT_FIND');
        }
        return $this->$method($inputData);
    }

    /**
     * @param $inputData
     * 微信统一下单--公众平台jsapi
     */
    public function unifiedWechatJsapi($inputData)
    {
        //获取openid 进行支付
        try {
            $bool = \App\Logic\Common\ShoppingLogic::isLogin('userId');
            if (!$bool) {
                throw new RJsonError('请先登录', 'LOGIN_ERROR');
            }
            $userInfo = (new UserModel)->where('user_id', $bool)->first();
            if (empty($userInfo)) {
                throw new Exception("用户信息为空");
            }
            if (empty($userInfo->user_openid)){
                throw new Exception("openid为空",'OPENID_EMPTY');
            }
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(),$exception->getCode());
        }
        $paymentWxpayCommonLogic = new PaymentWxpayCommonLogic();
        $res = $paymentWxpayCommonLogic->unified($this, $userInfo->user_openid);
        return $res;
    }

    /**
     * 获取订单数据
     * 返回model
     */
    public function getOneByAuthCode()
    {
        $this->orderModel = $this->getOrderModel();
        return $this->orderModel;
    }

    /**
     * @param $appName
     * @param $appType
     * @param $paymentType
     * 获取支付方式
     */
    public function getPaymentTypedBy($appName = '', $appType = 'mp', $paymentType = null)
    {
        if (empty($paymentType)) {
            $key = $appName . '_' . $appType;
            if (empty(OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE[$key])) {
                throw new Exception("不支持该支付方式[{$appName}-{$appType}]", 'PATMENT_TYPE_NOT_SUPPORTED');
            }
            $paymentType = OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE[$key];
        }
        $paymentType = (int)$paymentType;
        if (!in_array($paymentType, OrderCommonModel::APP_NAME_TYPE_TO_PATMENT_TYPE, true)) {
            throw new Exception("不支持该支付方式[{$paymentType}]", 'PATMENT_TYPE_NOT_SUPPORTED');
        }
        return $paymentType;
    }

    /**
     * @return bool|void
     * 判断是否支付成功
     */
    public function checkPaymentSuccess()
    {
        /**
         * 判断该订单的支付状态，如果是支付状态为空,或者是支付状态小于1的话
         * 重新获取数据获取该订单的数据
         * 如果订单状态大于1 的话，直接返回成功了。
         */
        if (empty($this->paymentState) || $this->paymentState < OrderCommonModel::ORDER_STATE_PAY_SUCCESS) {
            $this->load($this->getOrderModel()->toHumpArray()); //加载数据模型
        }
        if ($this->paymentState >= OrderCommonModel::ORDER_STATE_PAY_SUCCESS) { //订单状态大于1表示已经支付成功了
            return true;
        }
        /**
         * 取得支付方式，得到之前的支付方式。
         * 循环每一种支付方式进行调用,在对应方法里面的checkPaymentSuccess
         * 进行判断，如果是下单操作的话，直接抛出异常给外部程序捕获
         * 如果是支付回调的话，直接进行下面的操作:
         *
         */
        $checkClaeeArrays = $this->getCheckClassArrays();
        isset($checkClaeeArrays) && \Log::info($checkClaeeArrays); //打印变量
        $res = null;
        foreach ($checkClaeeArrays as $item) {
            try {
                if (class_exists($item)) {
                    $checkLogic = new $item([
                        'orderNumber' => $this->orderNumber
                    ]);
                    if (method_exists($checkLogic, 'checkPaymentSuccess')) {
                        $res = $checkLogic->checkPaymentSuccess($this);
                        break;
                    }
                }
            } catch (\Throwable $e) {
                if (env('APP_DEBUG')) {
                    \Log::info('仅仅调试期间使用打印 异常 - all - checkPaymentSuccess');
                    \Log::info($e->getMessage());
                    \Log::info($e->getLine());
                    \Log::info($e->getFile());
                }
            }
        }
        if (empty($res)) {
            throw new PaymentNotPay("尚未支付", 'ORDER_NOT_PAYMENT');
        }
        \Log::info(['res' => $res]);
        $this->paymentState = $res['paymentState'];
        $this->paymentAt = $res['paymentAt'];
        //判断支付状态（判断微信或者支付宝里面的订单数据状态）-- 这表示未支付的
        if ($this->paymentState <= OrderCommonModel::PATMENT_STATE_UNPAY) {
            throw new PaymentNotPay("尚未支付");
        }
        // 如果已经支付，可是订单数据库中的数据还是没有支付成功的状态才修改数据
        if ($this->orderState < OrderCommonModel::ORDER_STATE_PAY_SUCCESS) {
            $this->orderState = OrderCommonModel::ORDER_STATE_PAY_SUCCESS; //修改订单状态 未支付成功
            $this->paymentType = OrderCommonModel::ORDER_STATE_PAY_SUCCESS; //修改支付方式
            $this->update();
            $this->orderEventPaySuccess();
        }
    }

    /**
     * @return array|void
     * 获取支付集合方式
     */
    public function getCheckClassArrays()
    {
        if (empty($this->paymentTypesTry)) {
            throw new PaymentNotPay("尚未支付", 'ORDER_NOT_PAYMENT');
        }
        $res = [];
        /**
         * 循环加载以前的支付方式，重新支付
         */
        foreach (explode(',', $this->paymentTypesTry) as $paymentType) {
            if (!empty(OrderCommonModel::PATMENT_TYPE_TO_CHECK_CLASS_NAME[$paymentType])) {
                $res [] = OrderCommonModel::PATMENT_TYPE_TO_CHECK_CLASS_NAME[$paymentType];
            }
        }
        if (empty($res)) {
            throw new PaymentNotPay("尚未支付", 'ORDER_NOT_PAYMENT');
        }
        return $res;
    }

    /**
     * 此处做商品的库存减少等操作
     */
    public function orderEventPaySuccess()
    {

    }

    /**
     *
     */
    public function paymentHandle($queryMethod,$orderNumber,\Request $request =null){
        if (empty($orderNumber)) {
            throw new Exception("没有找到订单",'ERROR_NUMBER_NOT_FOUND');
        }
        if (empty($queryMethod)) {
            throw new Exception('没有找到该支付回调查询方法', 'QUERY_METHOD_NOT');
        }
        //请求数据为空
        if (empty($request)){
            $request = Request();
        }
        $method = 'paymentHandle'.ucfirst($queryMethod); //转换为对应的方法 ，类似于 paymentHandleWechat paymentHandleAliPay
        //判断是否支持该下单方式
        if (!method_exists($this,$method)) {
            throw new Exception("暂时不支持该支付类型",'METHOD_NOT_FIND');
        }
        $this->orderNumber = $orderNumber;
        try{
            \DB::beginTransaction();
            //查询得到当前订单数据
            $this->load($this->getOrderModel()->toHumpArray());
            \Log::info('paymentHandle');
            \Log::info('queryMethod');
            \Log::info($queryMethod);
            \Log::info('orderNumber');
            \Log::info($orderNumber);
            $this->$method(function($res){
                $this->checkPaymentSuccess();
            },$request);
            \DB::commit();
        }catch (\Throwable $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }
    protected function paymentHandleWechat(\Closure $checkCallback,\Request $request){
        $paymentWxpayCommonLogic = new PaymentWxpayCommonLogic();
        return $paymentWxpayCommonLogic->paymentHandle($this, $checkCallback);
    }
}
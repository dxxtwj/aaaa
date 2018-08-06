<?php
namespace App\Logic\Shopping\Common\Payment;

use App\Logic\Exception;
use App\Logic\Shopping\Common\LoadDataLogic;


class PaymentQpayCommonLogic extends LoadDataLogic
{
    //付款ID 和payment表关联
    protected $orderNumber;
    // 原始订单号
    protected $orderNumberOrigin = '';
    // 设备号
    protected $deviceInfo = '';
    // 支付场景 : MICROPAY、APP、JSAPI、NATIVE
    protected $tradeType = '';
    // 交易状态 SUCCESS REFUND REVOKED CLOSED USERPAYING
    protected $tradeState = '';
    // 买家支付宝账号
    protected $buyerLogonId = '';
    // 付款银行
    protected $bankType = '';
    // 货币类型
    protected $feeType = '';
    // 商户订单总金额，单位为分
    protected $totalFee = '';
    // 用户实际支付金额
    protected $cashFee = '';
    // QQ钱包优惠金额
    protected $couponFee = '';
    // QQ钱包订单号
    protected $transactionId = '';
    // 商户订单号
    protected $outTradeNo = '';
    // 交易状态描述
    protected $tradeStateDesc = '';
    // 支付完成时间
    protected $timeEnd = '';
    // 用户标志
    protected $openId = '';
    /**
     * @return $model
     * @throws Exception
     */
    public function getOne(){
        if (!empty($this->orderNumber)){
            $model = (new PaymentQpayCommonModel())->where(['order_number' => $this->orderNumber])->firstHump();
        }elseif (!empty($this->orderNumberOrigin)){
            $model = (new PaymentQpayCommonModel())->where(['order_number_origin' => $this->orderNumberOrigin])->firstHump();
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
            $model = new PaymentQpayCommonModel();
        }
        // 读取数据
        $data = $this->getAttributes(null, ['', null]);
        $model->setDataByArray($data);
        $model->toUnderline();
        if(!$model->save()){
            throw new Exception('操作失败', 'SAVE_FAIL');
        }
        return true;
    }
}

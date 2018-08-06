<?php

namespace App\Model\V1\Payment;

use App\Logic\Exception;

class PaymentModel extends \App\Model\Model
{
    protected $table = 'payment';

    protected $primaryKey = 'payment_id';

    protected $dateFormat = 'U';

    //超时
    const TIMEOUT = -1;

    //未付款
    const UNPAID= 0;

    //付款成功
    const PAYSUCCESSFUL = 1;

    //退款一部分
    const REFUNDPART = 2;

    //全额退款
    const REFUNDALL = 3;
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
     * @param $orderNumber
     * @param $paymentType
     * @param $orderAmount
     * @param $siteId
     * @return PaymentModel
     * @throws Exception
     */
    public static function _create($orderNumber, $paymentType, $orderAmount, $siteId){
        $paymentModel = (new PaymentModel())->where('order_number',$orderNumber)->first();
        if (empty($paymentModel)){
            $paymentModel = new PaymentModel();
        }
        if ($paymentModel->payment_state == PaymentModel::PAYSUCCESSFUL){
            throw new Exception('该订单已经支付过，请不要重复支付', 'TRADE_HAS_SUCCESS');
        }
        $paymentModel->order_number = $orderNumber;
        $paymentModel->payment_type = $paymentType;
        $paymentModel->payment_state = self::UNPAID;
        $paymentModel->payment_money = $orderAmount;
        $paymentModel->site_id = $siteId;
        if (!$paymentModel->save()){
            throw new Exception('创建订单失败', 'ADD_PAYMENT_ERROR');
        }
        return $paymentModel;
    }
}

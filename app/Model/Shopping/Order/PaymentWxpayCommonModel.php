<?php
namespace App\Model\Shopping\Order;
use App\Model\Model;

class PaymentWxpayCommonModel extends Model{

    protected $table = 'payment_wxpay';

    protected $primaryKey = 'order_number';

    public $timestamps = false;
}
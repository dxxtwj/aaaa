<?php

namespace App\Model\V1\Order;

use App\Model\Model;
use App\Model\V1\Order\OrderGoodsModel;
use App\Model\V1\Order\OrderDeliveryModel;
use App\Model\V1\User\InfoModel;
use App\Model\V1\Delivery\DeliveryModel;

class OrderModel extends Model
{
    protected $table = 'order';

    protected $primaryKey = 'order_auto_id';

    protected $dateFormat = 'U';

    const  PAY_ALIPAY = 'aliPay';

    const PAY_SUCCES_STATUS = 1;

    const NOT_PAY = 0;

    const TIME_OUT_STATE = 'pc';

    const REFUND_STATE = 2;

    const COMPUTER_DEVICE_TYPE = 'pc';

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

    //获取订单产品
    public function getOrderGoodsList(){
        return $this->hasMany(OrderGoodsModel::class, 'order_id', 'order_id');
    }

    //获取订单产品
    public function getOrderGoods(){
        return $this->hasMany(OrderGoodsModel::class, 'order_id', 'orderId');
    }
    //获取一条订单商品
    public function orderGoods(){
        return $this->hasOne(OrderGoodsModel::class, 'order_id', 'order_id');
    }

    //获取订单用户信息
    public function getOrderUser(){
        return $this->hasOne(InfoModel::class,'uid','uid');
    }

    //物流信息
    public function getOrderDelivery(){
        return $this->hasOne(OrderDeliveryModel::class,'order_id','orderId');
    }

    //物流公司名称
    public function getOrderDeliveryCompany(){
        return $this->belongsTo(DeliveryModel::class,'order_id','orderId');
    }
}

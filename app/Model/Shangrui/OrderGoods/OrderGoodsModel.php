<?php

namespace App\Model\Shangrui\OrderGoods;


use App\Model\Shangrui\Goods\GoodsModel;
use App\Model\Shangrui\Order\OrderModel;

class OrderGoodsModel extends \App\Model\Model
{
    protected $table='app_order_goods';
    protected $primaryKey='order_goods_id';
    protected $dateFormat = 'U';
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


    public function order() {

        return $this->belongsTo(OrderModel::class, 'order_id', 'order_id');
    }


}
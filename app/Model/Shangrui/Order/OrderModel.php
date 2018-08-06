<?php

namespace App\Model\Shangrui\Order;


use App\Model\Shangrui\OrderGoods\OrderGoodsModel;

class OrderModel extends \App\Model\Model
{
    protected $table='app_order';
    protected $primaryKey='order_id';
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

    public function orderGoods() {

        return $this->hasMany(OrderGoodsModel::class, 'order_id', 'order_id');
    }

}
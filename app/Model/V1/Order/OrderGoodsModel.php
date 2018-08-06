<?php

namespace App\Model\V1\Order;

use App\Model\Model;
class OrderGoodsModel extends Model
{
    protected $table = 'order_goods';
    protected $primaryKey = 'order_goods_id';

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
}

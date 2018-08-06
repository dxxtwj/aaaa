<?php

namespace App\Model\V1\Order;

use App\Model\Model;

class OrderDeliveryModel extends Model
{
    protected $table = 'order_delivery';
    protected $primaryKey = 'order_delivery_id';

    const INVOICE = 2;
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

<?php

namespace App\Model\Shangrui\Cart;


class CartModel extends \App\Model\Model
{
    protected $table='app_cart';
    protected $primaryKey='cart_id';
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

}
<?php

namespace App\Model\Shopping\UserDiscount;


class UserDiscountModel extends \App\Model\Model
{
    protected $table='shopping_user_discount';
    protected $primaryKey='user_discount_id';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
//    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
//    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

}
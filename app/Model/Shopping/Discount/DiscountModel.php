<?php

namespace App\Model\Shopping\Discount;


class DiscountModel extends \App\Model\Model
{
    protected $table='shopping_full_reduced';
    protected $primaryKey='full_reduced_id';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    public $timestamps = false;
}

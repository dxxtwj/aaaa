<?php

namespace App\Model\Shopping\Sell;


class SellModel extends \App\Model\Model
{
    protected $table='shopping_sell_number';
    protected $primaryKey='sell_number_id';
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
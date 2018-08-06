<?php

namespace App\Model\Shopping\CustomerService;


class CustomerServiceModel extends \App\Model\Model
{
    protected $table='shopping_customer_service';
    protected $primaryKey='customer_service_id';
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
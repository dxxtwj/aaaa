<?php

namespace App\Model\Shangrui\UserAddress;


class UserAddressModel extends \App\Model\Model
{
    protected $table='app_user_address';
    protected $primaryKey='address_id';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    public $timestamps = false;

}
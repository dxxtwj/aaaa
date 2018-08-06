<?php

namespace App\Model\V1\User;
use \App\Model\Model;

class PhoneRegisteringModel extends Model
{
    protected $table = 'phone_registering';
    protected $primaryKey = 'id';
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

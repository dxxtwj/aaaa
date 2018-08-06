<?php
/**
 * Created by PhpStorm.
 * User: bo
 * Date: 2017/9/13
 * Time: 下午3:05
 */

namespace App\Model\V1\User;


class AddressModel extends \App\Model\Model
{
    protected $table = 'user_address';
    protected $primaryKey = 'address_id';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'register_time';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_time';
}
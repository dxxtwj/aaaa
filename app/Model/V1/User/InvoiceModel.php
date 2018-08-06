<?php
/**
 * Created by PhpStorm.
 * User: bo
 * Date: 2017/9/14
 * Time: 上午9:29
 */

namespace App\Model\V1\User;


class InvoiceModel extends \App\Model\Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'invoice_id';
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
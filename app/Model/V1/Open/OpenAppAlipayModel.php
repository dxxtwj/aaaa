<?php

namespace App\Model\V1\Open;

use App\Model\Model;

class OpenAppAlipayModel extends Model
{
    protected $table = 'open_app_alipay';
    protected $primaryKey = 'alipay_app_id';
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
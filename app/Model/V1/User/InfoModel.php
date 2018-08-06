<?php

namespace App\Model\V1\User;

class InfoModel extends \App\Model\Model
{
    protected $table = 'user_info';
    protected $primaryKey = 'uid';
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

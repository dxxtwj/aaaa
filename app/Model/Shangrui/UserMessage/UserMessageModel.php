<?php

namespace App\Model\Shangrui\UserMessage;


class UserMessageModel extends \App\Model\Model
{
    protected $table='app_user_message';
    protected $primaryKey='user_id';
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
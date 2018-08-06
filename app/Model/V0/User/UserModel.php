<?php

namespace App\Model\V0\User;


class UserModel extends \App\Model\Model
{
    protected $table='easy_user';
    protected $primaryKey='uid';
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
<?php

namespace App\Model\V0\User;


class UserInfoModel extends \App\Model\Model
{
    protected $table='easy_user_info';
    //protected $primaryKey='id';
    protected $dateFormat = 'U';
    //public $timestamps = false;
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
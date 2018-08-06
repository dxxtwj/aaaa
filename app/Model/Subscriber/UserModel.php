<?php

namespace App\Model\Subscriber;


class UserModel extends \App\Model\Model
{
    protected $table='user';
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

    public function Desc()
    {
        return $this->hasMany(UserDescModel::class, 'uid', 'uid');
    }
}
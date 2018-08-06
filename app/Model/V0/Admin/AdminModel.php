<?php

namespace App\Model\V0\Admin;


class AdminModel extends \App\Model\Model
{
    // 数据库'dadtabase_sencond'中的表
    protected $table='admin';
    protected $primaryKey='admin_id';
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

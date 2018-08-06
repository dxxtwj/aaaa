<?php

namespace App\Model\V0\Admin;


class AdminDescModel extends \App\Model\Model
{
    // 数据库'dadtabase_sencond'中的表
    protected $table='admin_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}

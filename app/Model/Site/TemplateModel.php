<?php

namespace App\Model\Site;

class TemplateModel extends \App\Model\Model
{
    // 数据库'dadtabase_center'中的site表
    //protected $connection = 'mysql_center';
    protected $table='template';
    protected $primaryKey='template_id';
    protected $dateFormat = 'U';
//    public $timestamps = false;
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
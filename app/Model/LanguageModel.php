<?php

namespace App\Model;

class LanguageModel extends \App\Model\Model
{
    // 数据库'dadtabase_center'中的表
    //protected $connection = 'mysql_center';
    protected $table='language';
    protected $primaryKey='language_id';
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

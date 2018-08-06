<?php

namespace App\Model\News;


class NewsDescModel extends \App\Model\Model
{
    /*protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
        parent::__construct();
    }*/
    protected $table='news_description';
    protected $primaryKey='news_desc_id';
    protected $dateFormat = 'U';
    public $timestamps = false;
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
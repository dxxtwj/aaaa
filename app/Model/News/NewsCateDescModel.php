<?php

namespace App\Model\News;


class NewsCateDescModel extends \App\Model\Model
{
    /*protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
        parent::__construct();
    }*/
    protected $table='news_category_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
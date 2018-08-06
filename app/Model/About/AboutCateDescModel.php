<?php

namespace App\Model\About;


class AboutCateDescModel extends \App\Model\Model
{
    //
    protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
        parent::__construct();
    }
//    protected $table='about_category_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
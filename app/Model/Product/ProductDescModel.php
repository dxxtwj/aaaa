<?php

namespace App\Model\Product;


class ProductDescModel extends \App\Model\Model
{
    /*protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
        parent::__construct();
    }*/
    protected $table='product_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
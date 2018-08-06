<?php

namespace App\Model\About;


class AboutCateModel extends \App\Model\Model
{
    protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
//        var_dump(66, $this->table);
        parent::__construct();
    }
//    protected $table='about_category';
    protected $primaryKey='about_cate_id';
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
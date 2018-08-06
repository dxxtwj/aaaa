<?php

namespace App\Model\Works;


class WorksCateModel extends \App\Model\Model
{
    protected $table='works_category';
    protected $primaryKey='works_cate_id';
    protected $dateFormat = 'U';
    //public $timestamps = false;
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
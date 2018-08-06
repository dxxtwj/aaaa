<?php

namespace App\Model\Basic;


class BasicDescModel extends \App\Model\Model
{
    protected $table='basic_description';
    protected $primaryKey='id';
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
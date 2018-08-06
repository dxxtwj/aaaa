<?php

namespace App\Model\V2\Kujiale;


class KujialeModel extends \App\Model\Model
{
    protected $table='kujiale';
    protected $primaryKey='kujiale_id';
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
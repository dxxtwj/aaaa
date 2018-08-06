<?php

namespace App\Model\V0\Site;

class SiteDefaultModel extends \App\Model\Model
{
    protected $table='site_default';
    protected $primaryKey='id';
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

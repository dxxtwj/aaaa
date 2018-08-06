<?php

namespace App\Model\SiteAllocation;


class SiteSeoModel extends \App\Model\Model
{
    protected $table='site_seo';
    protected $primaryKey='seo_id';
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
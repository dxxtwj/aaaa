<?php

namespace App\Model\V1\Site;

use App\Model\Model;

class SiteModel extends Model
{
    protected $table = 'site';
    protected $primaryKey = 'site_id';
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
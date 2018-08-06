<?php

namespace App\Model\About;


class AboutBannerModel extends \App\Model\Model
{
    protected $table='about_banner';
    protected $primaryKey='about_banner_id';
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

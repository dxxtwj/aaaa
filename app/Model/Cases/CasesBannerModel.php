<?php

namespace App\Model\Cases;


class CasesBannerModel extends \App\Model\Model
{
    protected $table='cases_banner';
    protected $primaryKey='cases_banner_id';
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
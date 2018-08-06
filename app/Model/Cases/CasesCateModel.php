<?php

namespace App\Model\Cases;


class CasesCateModel extends \App\Model\Model
{
    protected $table='cases_category';
    protected $primaryKey='cases_cate_id';
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
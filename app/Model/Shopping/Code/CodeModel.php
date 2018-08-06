<?php

namespace App\Model\Shopping\Code;


class CodeModel extends \App\Model\Model
{
    protected $table='shopping_code';
    protected $primaryKey='code_id';
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
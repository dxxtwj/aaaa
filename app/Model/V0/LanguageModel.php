<?php

namespace App\Model\V0;

class LanguageModel extends \App\Model\Model
{
    protected $table='language';
    protected $primaryKey='language_id';
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

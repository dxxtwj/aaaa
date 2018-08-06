<?php

namespace App\Model\V0\Site;

class DomainModel extends \App\Model\Model
{
    protected $table='domain';
    protected $primaryKey='domain_id';
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

<?php

namespace App\Model\V1\Domain;

use App\Model\Model;

class DomainModel extends Model
{
    protected $table = 'domain';
    protected $primaryKey = 'domain_id';
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
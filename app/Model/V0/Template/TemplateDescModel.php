<?php

namespace App\Model\V0\Template;

class TemplateDescModel extends \App\Model\Model
{
    protected $table='template_description';
    protected $primaryKey='template_desc_id';
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

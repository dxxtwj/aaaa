<?php

namespace App\Model\V0\Template;

class TemplateModel extends \App\Model\Model
{
    protected $table='template';
    protected $primaryKey='template_id';
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

    public function templateDesc()
    {
        return $this->hasMany(TemplateDescModel::class, 'template_id', 'templateId');
    }

}

<?php

namespace App\Model\V0\Module;

class ModuleModel extends \App\Model\Model
{
    protected $table='module';
    protected $primaryKey='model_id';
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

    public function moduleDetails(){
        return $this->hasMany(ModuleDetailsModel::class, 'model_id', 'modelId');
    }

}

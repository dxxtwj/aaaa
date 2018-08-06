<?php
namespace App\Model\V0\Feature;

class FeatureModel extends \App\Model\Model
{
    // 数据库'dadtabase_autostation_cs'中的表
    protected $table='feature';
    protected $primaryKey='feature_id';
    protected $dateFormat = 'U';


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
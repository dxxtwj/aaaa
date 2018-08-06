<?php

namespace App\Model\Shopping\Type;


use App\Model\Shopping\Goods\GoodsModel;

class TypeModel extends \App\Model\Model
{
    protected $table='shopping_type';
    protected $primaryKey='type_id';
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

    public function goodsData() {

        return $this->hasOne(GoodsModel::class, 'type_id', 'type_id');
    }

}
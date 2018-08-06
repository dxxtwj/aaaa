<?php

namespace App\Model\Shopping\UserMessage;


use App\Model\Shopping\Goods\GoodsModel;
use App\Model\Shopping\UserMessage\UserMessageModel;

class UserMessageModel extends \App\Model\Model
{
    protected $table='shopping_user_message';
    protected $primaryKey='user_opinion_id';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
//    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
//    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

}
<?php

namespace App\Model\Message;


class MessageCategoryModel extends \App\Model\Model
{
    protected $table='message_category';
    protected $primaryKey='message_cate_id';
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
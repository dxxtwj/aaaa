<?php

namespace App\Model\Shopping\WechatNotify;


class WechatNotifyModel extends \App\Model\Model
{
    protected $table='shopping_wechat_notify';
    protected $primaryKey='wechat_notify_id';
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
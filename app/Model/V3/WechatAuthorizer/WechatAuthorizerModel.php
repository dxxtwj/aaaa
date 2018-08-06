<?php

namespace App\Model\V3\WechatAuthorizer;

class WechatAuthorizerModel extends \App\Model\Model
{
    // 数据库'dadtabase_center'中的site表
    protected $table='wechat_authorizer';
    protected $primaryKey='wechat_authorizer_id';
    protected $dateFormat = 'U';
    //public $timestamps = false;
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

<?php

namespace App\Model\V0\Wechat;

class AppletAuditidModel extends \App\Model\Model
{
    protected $table='open_applet_auditid';
    protected $primaryKey='wechat_app_id';
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
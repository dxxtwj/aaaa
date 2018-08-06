<?php

namespace App\Model\V1\User;

class OauthAlipayModel extends \App\Model\Model
{
    protected $table = 'user_oauth_alipay';
    protected $primaryKey = 'uoaid';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'register_time';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'last_login_at';
}

<?php
/**
 * Created by PhpStorm.
 * User: bo
 * Date: 2017/8/30
 * Time: 下午3:52
 */

namespace App\Model\V1\User;


class SafeQuestionModel extends \App\Model\Model
{
    protected $table = 'user_safe_question';
    protected $primaryKey = 'usqid';
    protected $dateFormat = 'U';
}
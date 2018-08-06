<?php
/**
 * Created by PhpStorm.
 * User: bo
 * Date: 2017/8/30
 * Time: 下午3:53
 */

namespace App\Model\V1\User;


class SafeQuestionListsModel extends \App\Model\Model
{
    protected $table = 'user_safe_question_lists';
    protected $primaryKey = 'question_id';
    protected $dateFormat = 'U';
}
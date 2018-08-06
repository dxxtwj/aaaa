<?php
/**
 * Created by PhpStorm.
 * User: bo
 * Date: 2017/9/5
 * Time: 下午4:41
 */

namespace App\Model\V1\User;


class HeadImgModel extends \App\Model\Model
{
    protected $table = 'user_head_img';
    protected $primaryKey = 'id';
    protected $dateFormat = 'U';

}
<?php

namespace App\Model\V10;

use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    // 数据库'dadtabase_center'中的site表
    protected $connection = 'mysql_star';
    protected $table='file';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}

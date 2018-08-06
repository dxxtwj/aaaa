<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    protected $table='file';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}

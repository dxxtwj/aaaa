<?php

namespace App\Model\Admin;


class AdminDescModel extends \App\Model\Model
{
    protected $table='admin_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}

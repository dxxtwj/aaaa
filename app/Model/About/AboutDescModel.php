<?php

namespace App\Model\About;

class AboutDescModel extends \App\Model\Model
{
    protected $table='about_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
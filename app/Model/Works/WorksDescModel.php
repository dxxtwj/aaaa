<?php

namespace App\Model\Works;


class WorksDescModel extends \App\Model\Model
{
    protected $table='works_description';
    protected $primaryKey='works_desc_id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
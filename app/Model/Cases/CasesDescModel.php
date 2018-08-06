<?php

namespace App\Model\Cases;


class CasesDescModel extends \App\Model\Model
{
    protected $table='cases_description';
    protected $primaryKey='cases_desc_id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
<?php

namespace App\Model\Cases;


class CasesCateDescModel extends \App\Model\Model
{
    protected $table='cases_category_description';
    protected $primaryKey='id';
    protected $dateFormat = 'U';
    public $timestamps = false;

}
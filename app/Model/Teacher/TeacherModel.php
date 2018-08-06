<?php

namespace App\Model\Teacher;


class TeacherModel extends \App\Model\Model
{
    protected $table='teacher';
    protected $primaryKey='teacherid';
    protected $dateFormat = 'U';
    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

}
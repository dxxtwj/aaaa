<?php

namespace App\Model\V0\OurMessage;


class OurmessageModel extends \App\Model\Model
{
    protected $table='our_message';
    protected $primaryKey='our_message_id';
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
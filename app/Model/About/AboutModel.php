<?php

namespace App\Model\About;


use App\Logic\BannerLogic;

class AboutModel extends \App\Model\Model
{
    protected $table='about';
    protected $primaryKey='about_id';
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

    public function desc()
    {
        return $this->hasMany(AboutDescModel::class, 'about_id', 'aboutId');
    }
    public function banner()
    {
        return $this->hasMany(AboutBannerModel::class, 'about_id', 'aboutId');
    }
}

<?php

namespace App\Model\News;


class NewsModel extends \App\Model\Model
{
    /*protected  $table = 'default_table';
    private static $prefix;
    public function __construct($prefix)
    {
        self::$prefix = $prefix;
        $this->table = self::$prefix;
        parent::__construct();
    }*/
    protected $table='news';
    protected $primaryKey='news_id';
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

    public function banner()
    {
        return $this->hasMany(NewsBannerModel::class, 'news_id', 'newsId')->orderBy('sort', 'DESC');
    }
    public function photos()
    {
        return $this->hasMany(NewsimageModel::class, 'news_id', 'newsId');
    }
    public function desc()
    {
        return $this->hasMany(NewsDescModel::class, 'news_id', 'newsId');
    }

}
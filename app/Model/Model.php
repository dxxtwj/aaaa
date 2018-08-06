<?php

namespace App\Model;
use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

/**
 * @mixin \DdvPhp\DdvUtil\Laravel\EloquentBuilder
 * @mixin \DdvPhp\DdvUtil\Laravel\QueryBuilder
 * @mixin \App\Model\QueryBuilder
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use \DdvPhp\DdvUtil\Laravel\Model;

    /**
     * 设置保存数据 驼峰自动转小写下划线
     * @param array $data [需要保存的数组]
     * @return \Illuminate\Database\Eloquent\Model $this [请求对象]
     */
    public function setSiteId ($siteId = null) {
        if (empty($siteId)){
            if (@$siteId===null){
                $siteId = SiteId::getSiteId();
            }
            if (empty($siteId)){
                throw new RJsonError('没有找到该网站', 'SITE_ID_MUST_INPUT');
            }
        }
        $this->site_id = $siteId;
        return $this;
    }
    /**
     * Get a new query builder instance for the connection.
     *
     * @return \App\Model\QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }

    public static $getCalledClassModel = [];
    /**
     * @return \App\Model\Model Model
     */
    public static function getCalledClassModel(){
        $class = get_called_class();
        if (!(isset(self::$getCalledClassModel[$class])&&self::$getCalledClassModel[$class] instanceof \App\Model\Model)){
            self::$getCalledClassModel[$class] = new $class;
        }
        return self::$getCalledClassModel[$class];
    }
    public static function tableName(){
        $model = self::getCalledClassModel();
        $table = $model->getTable();
        return $table;
    }
    public static function keyName(){
        $model = self::getCalledClassModel();
        $keyName = $model->getKeyName();
        return $keyName;

    }

}

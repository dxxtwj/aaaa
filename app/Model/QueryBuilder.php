<?php
/**
 * Created by PhpStorm.
 * User: hua
 * Date: 2017/8/24
 * Time: 下午4:23
 */

namespace App\Model;

use App\Http\Middleware\SiteId;
use DdvPhp\DdvRestfulApi\Exception\RJsonError;

class QueryBuilder extends \DdvPhp\DdvUtil\Laravel\QueryBuilder
{
    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function whereSiteId($siteId = null, $keyName = 'site_id')
    {
        if (empty($siteId)){
            if (@$siteId===null){
                $siteId = SiteId::getSiteId();
            }
            if (empty($siteId)){
                throw new RJsonError('没有找到该网站', 'SITE_ID_MUST_INPUT');
            }
        }
        return $this->where($keyName, $siteId);
    }
    public function whereSiteIdBySiteName($keyName = 'site_id', $siteId = null){
        return $this->whereSiteId($siteId, $keyName);
    }
}

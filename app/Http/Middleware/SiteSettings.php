<?php

namespace App\Http\Middleware;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \Closure;
use App\Logic\V1\Site\SiteLogic;

class SiteSettings
{
    /**
     * @var int 站点ID
     */
    protected static $siteId = 0;

    /**
     * @var int 语言ID
     */
    protected static $languageId = 0;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        $fromHost = !empty($request->header('x-ddv-from-host')) ? $request->header('x-ddv-from-host') : (!empty($request->header('x-ddv-from-origin')) ? $request->header('x-ddv-from-origin') : $request->header('x-ddv-from-referer'));
        if (empty($fromHost) && !empty($request->header('Referer'))){
            $fromHost = $request->header('referer');
        }
        if (empty($fromHost) && !empty($request->header('origin'))){
            $fromHost = $request->header('Origin');
        }
        if (empty($fromHost) && !empty($request->header('x-ddv-from-origin'))){
            $parseArray = parse_url($fromHost);
            $fromHost = $parseArray['host'];
        }
        if(empty($fromHost)){
            throw new RJsonError('站点域名为空', 'NOT_FIND_HOST');
        }
        $fromLang = $request->header('x-ddv-from-lang') ? $request->header('x-ddv-from-lang') : $request->header('accept-language');
        if (empty($fromLang)){
            $fromLang = 'cn';
        }else{
            $fromLang = strtok($fromLang,'.');
        }

        self::$siteId = SiteLogic::getSiteId($fromHost);
        self::$languageId = SiteLogic::getLanguageId('cn');
        return $next($request);
    }

    /**设置语言ID
     * @param string $lang
     */
    public static function setLanguageId($lang = 'cn'){
        if ($lang){
            self::$languageId =  SiteLogic::getLanguageId($lang);
        }
    }

    /**设置站点ID
     * @param null $siteId
     */
    public static function setSiteId($siteId = null){
        if ($siteId){
            self::$siteId = $siteId;
        }
    }

    /**
     * 获取语言id
     * @return null
     */
    public static function getLanguageId(){
        return self::$languageId;
    }

    /**
     * 获取站点id
     * @return null
     */
    public static function getSiteId(){
        return self::$siteId;
    }
}
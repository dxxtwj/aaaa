<?php

namespace App\Http\Middleware;
use App\Logic\LanguageLogic;
use \App\Logic\Site\DomainLogic;
use App\Logic\Site\SiteDefaultLogic;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \Closure;

class SiteId
{
    protected static $siteId = null;
    protected static $languageId = null;
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
        $FromHost = $request->header('x-ddv-from-host');
        if (empty($FromHost)){

            $FromHost = $request->header('x-ddv-from-origin');
            if(!empty($FromHost)){
                $arr = parse_url($FromHost);
                $FromHost=$arr['host'];
            }
        }
        if (empty($FromHost)){

            $FromHost = $request->header('x-ddv-from-referer');
        }

        $FromLang = $request->header('x-ddv-from-lang');
        if (empty($FromLang)){
            $FromLang = $request->header('accept-language');

        }

        if(isset($FromHost)){
            $SiteId=self::getDomainUrl($FromHost);
            $langName=strtok($FromHost,'.');
            if($langName=='zh' || $langName=='en' || $langName=='jp'){
                $LangId = self::getLangId($langName);
            }
        }
        if(empty($SiteId)){

            throw new RJsonError('网站维护中...........', 'NO_SITE');
        }

        self::$siteId = $SiteId;
        self::$languageId = empty($LangId) ? 1 : $LangId;
        //查网站添加默认页面的状态
        if(isset($SiteId)){
            $isAdd=self::getSiteDefault($SiteId);
            //如果等于0，说明网站刚刚添加，还没有单页面的数据，去添加默认数据
            if($isAdd==0){
                $langId=1;
                SiteDefaultLogic::addSiteDefault($SiteId,$langId);
                //添加过就改变状态
                SiteDefaultLogic::putSiteDefault($SiteId);
            }
        }
        return $next($request);
    }

    /**
     * 获取站点id
     * @return null
     */
    public static function getSiteId(){
        return self::$siteId;
    }
    public static function setSiteId($siteId){
        return self::$siteId = $siteId;
    }
    /**
     * 获取语言id
     * @return null
     */
    public static function getLanguageId(){
        return self::$languageId;
    }
    public static function setLanguageId($languageId){
        return self::$languageId = $languageId;
    }
    /**
     * 根据域名获取站点
     * @return null
     */
    public static function getDomainUrl($FromHost){
        $siteId=DomainLogic::getDomain($FromHost);
        return $siteId;
    }

    /**
     * 获取站点默认页面的状态
     * @return null
     */
    public static function getSiteDefault($siteId){
        $isAdd=SiteDefaultLogic::getSiteDefault($siteId);
        return $isAdd;
    }

    /**
     * 根据zh、en、jp获取语言
     * @return null
     */
    public static function getLangId($key){
        $languageId=LanguageLogic::getLanguageId($key);
        return $languageId;
    }



}

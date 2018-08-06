<?php

namespace App\Http\Middleware;
use \Illuminate\Http\Request;
use \Closure;
use \App\Http\Middleware;

class SiteIdBrush
{
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
        SiteId::setSiteId(21);
        SiteId::setLanguageId(1);
        return $next($request);
    }


}

<?php

namespace App\Http\Middleware;
use App\Logic\V10\LoginLogic;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \Closure;

class StarAdminLogin
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
        if (!LoginLogic::isLogin()) {
            throw new RJsonError('没有登录', 'NO_LOGIN');
        }

        return $next($request);
    }
}

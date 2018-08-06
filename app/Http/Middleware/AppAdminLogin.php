<?php

namespace App\Http\Middleware;
use App\Logic\Common\ShoppingLogic;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \Closure;

class AppAdminLogin extends ShoppingLogic
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
        $bool = self::isLogin('adminId');
        if ($bool == false) {

            throw new RJsonError('请先登录', 'LOGIN_ERROR');
        }

        return $next($request);
    }
}

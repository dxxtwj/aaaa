<?php

namespace App\Http\Middleware;
use App\Logic\Common\ShoppingLogic;
use \DdvPhp\DdvRestfulApi\Exception\RJsonError;
use \Illuminate\Http\Request;
use \Closure;

class ShoppingLogin extends ShoppingLogic
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
        $bool = self::isLogin('userId');
        if ($bool == false) {

            throw new RJsonError('请先登录', 'LOGIN_ERROR');
        }

        return $next($request);
    }
}

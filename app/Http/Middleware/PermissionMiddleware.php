<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware extends \Spatie\Permission\Middleware\PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        // 超级管理员直接放行
        if(auth()->user()->id == 1){
            return $next($request);
        }
        return parent::handle($request, $next, $permission, $guard);
    }
}

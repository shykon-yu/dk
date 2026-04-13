<?php
use Illuminate\Support\Facades\Auth;
function menu_route($routeName)
{
    $params = [];
    $needUserRoutes = [
        'admin.user.edit',
        'admin.user.show',
        'admin.user.update',
    ];

    // 如果路由需要用户ID → 自动注入当前登录用户ID
    if (in_array($routeName, $needUserRoutes)) {
        $params['user'] = Auth::id();
    }
    // 直接返回 route() 结果，不会转成URL！
    return route($routeName, $params);
}

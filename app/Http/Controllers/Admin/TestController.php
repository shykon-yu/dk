<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\MenuService;

class TestController extends Controller
{
    public function index(User $user,MenuService $menuService)
    {
        //$menuService->clearMenuCache();
        $menu = $menuService->getAuthMenu();
        dd($menu);
        //d(Auth::user()->getDeptIdArray());
    }
}

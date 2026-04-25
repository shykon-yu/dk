<?php

namespace App\Providers;

use App\Services\Admin\MenuService;
use App\Services\Admin\ViewDataService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(MenuService $menuService , ViewDataService $viewDataService)
    {
        view()->composer('admin.*', function ($view) use ($menuService, $viewDataService) {

            // 菜单
            if (auth()->check()) {
                //$menuService->clearMenuCache();
                $view->with('admin_menus', $menuService->getAuthMenu());
            } else {
                $view->with('admin_menus', []);
            }

            $view->with($viewDataService->getAllCommonData());
        });
    }
}

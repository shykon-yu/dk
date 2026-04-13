<?php

namespace App\Providers;

use App\Services\MenuService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(MenuService $menuService)
    {
        $menuService->clearMenuCache();
        view()->composer('admin.*', function ($view) use ($menuService) {
            if (auth()->check()) {
                //$menuService->clearMenuCache();
                $view->with('menu', $menuService->getAuthMenu());
            } else {
                $view->with('menu', []);
            }
        });
    }
}

<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\MenuController;

Route::prefix('admin')->name('admin.')->group(function () {

    // 1️⃣ 登录相关：无需登录 (guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.submit');
    });

    // 2️⃣ 后台功能：必须登录 (auth)
    Route::middleware('auth')->group(function () {
        //首页
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        //用户管理
        Route::resource('user', UserController::class); // 自动生成 index/create/store/show/edit/update/destroy
        Route::post('user/delete', [UserController::class, 'delete'])->name('user.delete');

        //角色管理
        Route::resource('role',RoleController::class)->only(['index', 'create' , 'edit' , 'store']);
        Route::post('role/delete', [RoleController::class, 'delete'])->name('role.delete');
        Route::post('role/update', [RoleController::class, 'update'])->name('role.update');

        Route::get('/test', [TestController::class, 'index'])->name('test');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        //菜单管理
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu/store', [MenuController::class, 'store'])->name('menu.store');
        Route::get('menu/edit/{menu}', [MenuController::class, 'edit'])->name('menu.edit');
        Route::post('/menu/update', [MenuController::class, 'update'])->name('menu.update');
        Route::post('/menu/destroy', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

});

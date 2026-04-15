<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\TestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\UserMigrationController;

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
        Route::get('/test', [TestController::class, 'index'])->name('test');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/migrate',[UserMigrationController::class,'migrate'])->name('migrate');

        require __DIR__ . '/admin/users.php';
        require __DIR__ . '/admin/goods.php';
    });

});

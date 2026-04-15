<?php
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MenuController;
//用户管理
Route::delete('users/batch', [UserController::class, 'batchDestroy'])->name('users.batch.destroy');
Route::resource('users', UserController::class); // 自动生成 index/create/store/show/edit/update/destroy

//角色管理
Route::delete('roles/batch', [RoleController::class, 'batchDestroy'])->name('roles.batch.destroy');
Route::resource('roles',RoleController::class);

//菜单管理
Route::delete('menus/batch', [MenuController::class, 'batchDestroy'])->name('menus.batch.destroy');
Route::resource('menus',MenuController::class);


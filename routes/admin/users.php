<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\Order\ClearanceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;

//用户管理
Route::delete('users/batch', [UserController::class, 'batchDestroy'])->name('users.batch.destroy');
Route::post('users/status/{user}',[UserController::class,'status'])->name('users.status');
Route::resource('users', UserController::class); // 自动生成 index/create/store/show/edit/update/destroy

//角色管理
Route::delete('roles/batch', [RoleController::class, 'batchDestroy'])->name('roles.batch.destroy');
Route::resource('roles',RoleController::class);

//菜单管理
Route::delete('menus/batch', [MenuController::class, 'batchDestroy'])->name('menus.batch.destroy');
Route::resource('menus',MenuController::class);

//客户管理
Route::delete('customers/batch', [CustomerController::class, 'batchDestroy'])->name('customers.batch.destroy');
Route::post('customers/status/{customer}',[CustomerController::class,'status'])->name('customers.status');
Route::resource('customers',CustomerController::class);

//供应商管理
Route::delete('suppliers/batch', [SupplierController::class, 'batchDestroy'])->name('suppliers.batch.destroy');
Route::post('suppliers/status/{supplier}',[SupplierController::class,'status'])->name('suppliers.status');
Route::resource('suppliers',SupplierController::class);

//部门管理
Route::delete('departments/batch', [DepartmentController::class, 'batchDestroy'])->name('departments.batch.destroy');
Route::post('departments/status/{department}',[DepartmentController::class,'status'])->name('departments.status');
Route::resource('departments',DepartmentController::class);

//仓库管理
Route::delete('warehouses/batch', [WarehouseController::class, 'batchDestroy'])->name('warehouses.batch.destroy');
Route::post('warehouses/status/{warehouse}',[WarehouseController::class,'status'])->name('warehouses.status');
Route::resource('warehouses',WarehouseController::class);

//清关管理
Route::delete('clearances/batch', [ClearanceController::class, 'batchDestroy'])->name('clearances.batch.destroy');
Route::post('clearances/status/{clearance}',[ClearanceController::class,'status'])->name('clearances.status');
Route::resource('clearances',ClearanceController::class);

//支付方式
Route::delete('payments/batch', [PaymentController::class, 'batchDestroy'])->name('payments.batch.destroy');
Route::post('payments/status/{payment}',[PaymentController::class,'status'])->name('payments.status');
Route::resource('payments',PaymentController::class);

<?php

use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Order\InboundController;

//订单
Route::post('orders/star/{order}',[OrderController::class,'star'])->name('orders.star');
Route::post('orders/status/{order}',[OrderController::class,'status'])->name('orders.status');
Route::get('orders/reorder/{order}',[OrderController::class,'reorder'])->name('orders.reorder');
Route::post('orders/upload/excel',[OrderController::class,'uploadExcel'])->name('orders.upload.excel');
Route::get('orders/items',[OrderController::class,'items'])->name('orders.items');
Route::resource('orders', OrderController::class);

//入库
Route::get('inbounds/items',[InboundController::class,'items'])->name('inbounds.items');
Route::resource('inbounds', InboundController::class);

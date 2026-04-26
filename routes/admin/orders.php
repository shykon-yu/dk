<?php

use App\Http\Controllers\Admin\Order\OrderController;

//入库
//Route::delete('orders/inbounds/batch',[GoodsSeasonController::class,'batchDestroy'])->name('goods.seasons.batch.destroy');
//Route::post('orders/inbounds/status/{inbound}',[GoodsSeasonController::class,'status'])->name('goods.seasons.status');
//Route::resource('orders/inbounds', GoodsSeasonController::class)->names('goods.seasons');

//订单
Route::resource('orders', OrderController::class);

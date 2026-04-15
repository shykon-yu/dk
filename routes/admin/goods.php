<?php

use App\Http\Controllers\Admin\Goods\GoodsSeasonController;
Route::delete('goods/seasons/batch',[GoodsSeasonController::class,'batchDestroy'])->name('goods.seasons.batch.destroy');
Route::post('goods/seasons/status/{season}',[GoodsSeasonController::class,'status'])->name('goods.seasons.status');
Route::resource('goods/seasons', GoodsSeasonController::class)->names('goods.seasons');


//Route::resource('goods/components', GoodsComponentController::class)->names([
//    'index'   => 'goods.components.index',
//    'create'  => 'goods.components.create',
//    'store'   => 'goods.components.store',
//    'edit'    => 'goods.components.edit',
//    'update'  => 'goods.components.update',
//    'destroy' => 'goods.components.destroy',
//]);

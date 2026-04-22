<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\Goods\GoodsController;
use App\Http\Controllers\Admin\Goods\GoodsSeasonController;
use App\Http\Controllers\Admin\Goods\GoodsCategoryController;
use App\Http\Controllers\Admin\Goods\GoodsComponentController;

//季节
Route::delete('goods/seasons/batch',[GoodsSeasonController::class,'batchDestroy'])->name('goods.seasons.batch.destroy');
Route::post('goods/seasons/status/{season}',[GoodsSeasonController::class,'status'])->name('goods.seasons.status');
Route::resource('goods/seasons', GoodsSeasonController::class)->names('goods.seasons');

//分类
Route::delete('goods/categories/batch',[GoodsCategoryController::class,'batchDestroy'])->name('goods.categories.batch.destroy');
Route::post('goods/categories/status/{category}',[GoodsCategoryController::class,'status'])->name('goods.categories.status');
Route::resource('goods/categories',GoodsCategoryController::class)->names('goods.categories');

//成分
Route::delete('goods/components/batch',[GoodsComponentController::class,'batchDestroy'])->name('goods.components.batch.destroy');
Route::post('goods/components/status/{component}',[GoodsComponentController::class,'status'])->name('goods.components.status');
Route::resource('goods/components', GoodsComponentController::class)->names('goods.components');

//商品
Route::delete('goods/batch',[GoodsController::class,'batchDestroy'])->name('goods.batch.destroy');
Route::post('goods/status/{goods}',[GoodsController::class,'status'])->name('goods.status');
Route::post('goods/star/{goods}',[GoodsController::class,'star'])->name('goods.star');
Route::post('goods/upload/image',[GoodsController::class,'uploadImage'])->name('goods.upload.image');
Route::resource('goods', GoodsController::class);

//货币

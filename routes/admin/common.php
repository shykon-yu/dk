<?php
use App\Http\Controllers\Admin\CommonController;
Route::post('common/customer-by-dept', [CommonController::class, 'getCustomerByDept'])->name('common.customer-by-dept');
Route::post('common/warehouse-by-dept', [CommonController::class, 'getWarehouseByDept'])->name('common.warehouse-by-dept');
Route::post('common/category-by-parent', [CommonController::class, 'getCategoryByParent'])->name('common.category-by-parent');

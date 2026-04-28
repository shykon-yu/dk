<?php
use App\Http\Controllers\Admin\CommonController;
//通过部门查询客户
Route::post('common/customer-by-dept', [CommonController::class, 'getCustomerByDept'])->name('common.customer-by-dept');
//通过部门查询仓库
Route::post('common/warehouse-by-dept', [CommonController::class, 'getWarehouseByDept'])->name('common.warehouse-by-dept');
//通过父级查询子集商品分类
Route::post('common/category-by-parent', [CommonController::class, 'getCategoryByParent'])->name('common.category-by-parent');
//通过输入框筛选商品
Route::post('common/goods-search', [CommonController::class, 'getGoodsSearch'])->name('common.goods-search');
//客户默认商品两百个
Route::post('common/customer-default-goods', [CommonController::class, 'getCustomerDefaultGoods'])->name('common.customer-default-goods');
//通过商品获取SKU
Route::post('common/sku-by-goods', [CommonController::class, 'getSkuByGoods'])->name('common.sku-by-goods');

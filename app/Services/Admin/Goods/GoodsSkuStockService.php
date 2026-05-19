<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsSkuStock;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GoodsSkuStockService extends BaseService{
    public function __construct()
    {
        $this->modelClass = GoodsSkuStock::class;
        $this->cacheKey = 'goods_sku_stock_all';
    }

    public function getCacheAll(){}

    public function getStockInfo($params)
    {
        try {
            return $this->getModelClass()::query()
                ->where('warehouse_id', $params['warehouse_id'])
                ->where('sku_id', $params['sku_id'])
                ->with('sku')
                ->first();
        } catch (\Exception $e) {
            throw new \Exception('获取失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

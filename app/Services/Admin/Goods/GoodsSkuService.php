<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsSeason;
use App\Models\Sku;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GoodsSkuService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Sku::class;
        $this->cacheKey = 'goods_skus_all';
    }

    public function getCacheAll()
    {
//        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
//
//        });
    }

    public function getSkus(array $params)
    {
        try {
            return $this->getModelClass()::query()
                ->where('goods_id', $params['goods_id'])
                ->when(!empty($params['warehouse_id']), function ($query) use ($params) {
                    //如果存在仓库，就说明是出库页传来的，只展示有库存的项
                    $query->whereHas('stocks', function ($q) use ($params) {
                       $q->where('warehouse_id', $params['warehouse_id'])
                           ->where('goods_id', $params['goods_id'])
                           ->where('stock', '>', 0);
                    });
                })
                ->get();
        } catch (\Exception $e) {
            throw new \Exception('获取失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

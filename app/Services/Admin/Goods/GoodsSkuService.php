<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsSeason;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GoodsSkuService extends BaseService{
    public function __construct()
    {
        $this->modelClass = GoodsSeason::class;
        $this->cacheKey = 'goods_skus_all';
    }

    public function getCacheAll()
    {
//        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
//
//        });
    }

    public function getSkuByGoods($goodsId)
    {
        dd($goodsId);
        try {
            $skus = [];
            return $skus;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('获取失败', $e->getMessage()));
        }
    }
}

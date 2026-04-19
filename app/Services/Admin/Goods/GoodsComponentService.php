<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsComponent;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;

class GoodsComponentService extends BaseService{
    public function __construct()
    {
        $this->modelClass = GoodsComponent::class;
        $this->cacheKey = 'goods_component_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return GoodsComponent::query()
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
        });
    }

    public function getGoodsComponentsList($params)
    {
        $data = $this->getAllWithoutTrashed(); // 从缓存拿全部
        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

}

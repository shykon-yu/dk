<?php
namespace App\Services\Admin\Goods;
use App\Models\CraftMethod;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;

class CraftMethodService extends BaseService{
    public function __construct()
    {
        $this->modelClass = CraftMethod::class;
        $this->cacheKey = 'goods_craft_method_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return CraftMethod::query()
                ->where('status', 1)
                ->get();
        });
    }

    public function getCraftMethodsList($params)
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

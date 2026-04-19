<?php
namespace App\Services\Admin\Goods;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use App\Models\Goods;

class GoodsService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Goods::class;
        $this->cacheKey = 'goods_all';
    }

    /**
     * 所有商品
     */
    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Goods::query()
                ->select(['id', 'name'])
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();
        });
    }

    public function getGoodsList($params)
    {
//        $data = $this->getAllWithoutTrashed(); // 从缓存拿全部
//
//        if (!empty($params['name'])) {
//            $data = $data->filter(function ($item) use ($params) {
//                return str_contains($item->name, $params['name']);
//            });
//        }

        $data = $this->modelClass::query()
            ->with('customer','department','supplier','category','season','skus','creator','updater')
            ->when()
            ->leftjoin('goods_seasons as gs', 'gs.id', '=', 'season_id')
            ->orderBy('gs.year','desc')
            ->orderBy('gs.season','desc')
            ->orderBy('status', 'desc')
            ->orderBy('sort', 'asc')
            ->select('goods.*')
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function getAllWithoutTrashed()
    {
        return $this->modelClass::query()
            ->with('customer','department','supplier','category','season','skus','creator','updater')
            ->leftjoin('goods_seasons as gs', 'gs.id', '=', 'season_id')
            ->orderBy('gs.year','desc')
            ->orderBy('gs.season','desc')
            ->orderBy('status', 'desc')
            ->orderBy('sort', 'asc')
            ->select('goods.*')
            ->get();
    }
}

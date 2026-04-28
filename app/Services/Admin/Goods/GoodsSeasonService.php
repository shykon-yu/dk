<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsSeason;
use App\Services\Admin\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class GoodsSeasonService extends BaseService{
    public function __construct()
    {
        $this->modelClass = GoodsSeason::class;
        $this->cacheKey = 'goods_seasons_all';
    }

    /**
     * 所有商品季节
     */
    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return GoodsSeason::query()
                ->where('status', 1)
                ->orderBy('year', 'desc')
                ->orderBy('season', 'desc')
                ->get();
        });
    }

    public function getGoodsSeasonsList($params)
    {
        $data = $this->getAllWithoutTrashed(); // 从缓存拿全部

        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        if (isset($params['year'])) {
            $data = $data->where('year', $params['year']);
        }

        if (isset($params['season'])) {
            $data = $data->where('season', $params['season']);
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
    /**
     * 年份列表
     */
    public function getYearsOptions()
    {
        return $this->getCacheAll()
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();     // 重置索引（可选，让数组索引从0开始）
    }

    public function getAllWithoutTrashed()
    {
        return GoodsSeason::query()
            ->orderBy('year', 'desc')
            ->orderBy('season', 'desc')
            ->get();
    }

    public function changeCurrent(Model $model, int $current)
    {
        try {
            $model->is_current = $current;
            $model->save();
            $this->clearCache();
            return $model;
        } catch (\Exception $e) {
            throw new \Exception($this->formatMsg('当季修改', $e->getMessage()));
        }
    }
}

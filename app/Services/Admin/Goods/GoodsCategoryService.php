<?php
namespace App\Services\Admin\Goods;
use App\Models\GoodsCategory;
use App\Services\Admin\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GoodsCategoryService extends BaseService{
    public function __construct()
    {
        $this->modelClass = GoodsCategory::class;
        $this->cacheKey = 'goods_category_all';
    }

    /**
     * 所有商品分类，一级二级树形结构
     */
    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return GoodsCategory::with('children')
                ->where('parent_id', 0)
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->orderBy('id', 'desc')
                ->get();
        });
    }

    public function getChildrenByParentId(int $parent_id): Collection
    {
        $data = $this->getCacheAll();
        $parent = $data->firstWhere('id', $parent_id);
        $children = $parent?$parent->children:collect();
        return $children;
    }

    public function getGoodsCategoriesList($params)
    {
        $data = $this->getAllWithoutTrashed(); // 从缓存拿全部

        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function getTopLevel()
    {
        return $this->getCacheAll();
    }

    public function getAllWithoutTrashed()
    {
        return $this->modelClass::with('children')
            ->where('parent_id', 0)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();
    }
}

<?php
namespace App\Services\Admin;
use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class SupplierService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Supplier::class;
        $this->cacheKey = 'goods_supplier_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Supplier::where('status',1)
                ->with('creator','updater')
                ->orderBy('sort','asc')
                ->get();
        });
    }

    public function getCSupplierssList($params)
    {
        $data = $this->getAllWithoutTrashed();
        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    //覆盖父方法，解决N+1
    public function getAllWithoutTrashed()
    {
        return $this->modelClass::query()
            ->with('creator','updater')
            ->orderBy('sort', 'asc')
            ->get();
    }
}

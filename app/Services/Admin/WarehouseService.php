<?php
namespace App\Services\Admin;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class WarehouseService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Warehouse::class;
        $this->cacheKey = 'goods_warehouse_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Warehouse::where('status',1)
                ->with('department')
                ->orderBy('sort','asc')
                ->get();
        });
    }

    public function getWarehousesList($params)
    {
        $data = $this->modelClass::query()
            ->when($params['name'] ?? '',function($q) use($params){
                $name = trim($params['name']);
                return $q->where('name','like','%'.$name.'%');
            })
            ->when(!empty($params['department_ids']),function($q) use($params){
                return $q->whereIn('department_id',$params['department_ids']);
            })
            ->with('department:id,name')
            ->orderBy('sort','asc')
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function getWarehouseByDepartmentId(int $departmentId): Collection
    {
        $data = $this->getCacheAll();
        $data = $departmentId?$data->where('department_id',$departmentId)->values():collect();
        return $data;
    }
}

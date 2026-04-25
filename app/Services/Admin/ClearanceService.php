<?php
namespace App\Services\Admin;
use App\Models\Clearance;
use Illuminate\Support\Facades\Cache;

class ClearanceService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Clearance::class;
        $this->cacheKey = 'clearance_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Clearance::where('status' , 1)
                ->orderBy('sort' , 'asc')
                ->get();
        });
    }

    public function getClearancesList($params)
    {
        $data = $this->modelClass::query()
            ->when(!empty($params['name']), function ($query) use ($params) {
                $query->where('name', 'like', '%'.trim($params['name']).'%');
            })
            ->orderBy('sort', 'asc')
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
}

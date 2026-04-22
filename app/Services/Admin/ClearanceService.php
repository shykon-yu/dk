<?php
namespace App\Services\Admin;
use App\Models\Clearance;
use Illuminate\Support\Facades\Cache;

class ClearanceService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Clearance::class;
        $this->cacheKey = 'goods_clearance_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Clearance::where('status' , 1)
                ->orderBy('sort' , 'asc')
                ->get();
        });
    }

    public function getClearanceList($params)
    {
        $data = $this->getAllWithoutTrashed();

        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
}

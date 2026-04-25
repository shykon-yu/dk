<?php
namespace App\Services\Admin;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DepartmentService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Department::class;
        $this->cacheKey = 'department_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Department::where('status', 1)->orderBy('sort', 'asc')->get();
        });
    }

    public function getDepartmentsList($params)
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

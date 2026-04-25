<?php
namespace App\Services\Admin;
use App\Models\Customer;
use App\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CustomerService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Customer::class;
        $this->cacheKey = 'customer_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Customer::where('status',1)
                ->with('department','clearance','payment','creator','updater')
                ->orderBy('sort','asc')
                ->get();
        });
    }

    public function getCustomersList($params)
    {
        $data = $this->modelClass::query()
            ->when(!empty($params['name']), function ($query) use ($params) {
                $query->where('name', 'like', '%'.trim($params['name']).'%');
            })
            ->when(!empty($params['department_ids']), function ($query) use ($params) {
                $query->whereIn('department_id', $params['department_ids']);
            })
            ->with('department','clearance','payment','creator','updater')
            ->orderBy('sort', 'asc')
            ->get();
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }

    public function getCustomerByDepartmentId(int $departmentId): Collection
    {
        $data = $this->getCacheAll();
        $data = $departmentId?$data->where('department_id',$departmentId)->values():collect();
        return $data;
    }
}

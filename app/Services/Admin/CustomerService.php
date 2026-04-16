<?php
namespace App\Services\Admin;
use App\Models\Customer;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class CustomerService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Customer::class;
        $this->cacheKey = 'goods_customer_all';
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
        $data = $this->getAllWithoutTrashed();
        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
}

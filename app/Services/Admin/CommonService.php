<?php
namespace App\Services\Admin;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\Admin\CustomerService;

class CommonService{
    public function getCustomerByDepartmentId(int $department_id): Collection
    {
        try {
            $data = app(CustomerService::class)->getCacheAll();
            $data = $department_id?$data->where('department_id',$department_id)->values():collect();
            return $data;
        } catch (\Exception $e) {
            throw new \Exception('获取失败，'.$e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

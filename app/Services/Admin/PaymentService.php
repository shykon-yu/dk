<?php
namespace App\Services\Admin;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Payment::class;
        $this->cacheKey = 'payment_all';
    }

    public function getCacheAll()
    {
        return Cache::remember($this->getFullCacheKey() , $this->cacheTtl , function(){
            return Payment::where('status', 1)
                ->orderBy('sort' , 'asc')
                ->get();
        });
    }

    public function getPaymentsList($params)
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

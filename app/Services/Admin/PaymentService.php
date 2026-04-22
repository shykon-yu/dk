<?php
namespace App\Services\Admin;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentService extends BaseService{
    public function __construct()
    {
        $this->modelClass = Payment::class;
        $this->cacheKey = 'goods_payment_all';
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
        $data = $this->getAllWithoutTrashed();
        if (!empty($params['name'])) {
            $data = $data->filter(function ($item) use ($params) {
                return str_contains($item->name, $params['name']);
            });
        }
        return $this->paginateCacheData($data, $params,$this->getPerPage());
    }
}

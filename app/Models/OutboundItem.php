<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use App\Services\Admin\Goods\GoodsService;
use App\Services\Admin\Goods\GoodsSkuService;
use App\Services\Admin\Goods\GoodsSkuStockService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OutboundItem extends Base
{
    use SoftDeletes , FormatTimeTrait;
    protected $fillable = ['outbound_id', 'brand_logo','warehouse_id' ,'goods_id', 'sku_id','shipping_mark','carton_no_start','carton_no_end',
        'carton_qty','unit_carton_qty','carton_length','carton_width','carton_height','cbm','currency_id' ,'quantity', 'price', 'amount',
        'craft_method_id','gross_weight','net_weight','status', 'remark',];
    protected $dates = ['deleted_at'];
    public function scopeDepartmentAuth($query)
    {
        // 防止未登录报错，同时获取用户部门ID数组
        $deptIds = Auth::check() ? Auth::user()->getDeptIdArray() : [];
        if (Auth::user()->id === 1) {
            return;
        }
        return $query->whereHas('order', function ($query) use ($deptIds) {
            $query->whereIn('department_id', $deptIds);
        });
    }

    public function getAvailableGoodsAttribute()
    {
        $customerId = $this->outbound->customer_id;
        $warehouseId = $this->warehouse_id;
        $params = ['customer_id' => $customerId, 'warehouse_id' => $warehouseId];
        // 查出：当前客户 + 当前仓库 + 库存>0 的商品
        $goods = app(GoodsService::class)->getCustomerGoodsWithStock($params);

        // 如果当前选中商品不在列表 → 追加进去
        $currentGoods = $this->goods;
        if ($currentGoods && !$goods->contains('id', $currentGoods->id)) {
            $goods->prepend($currentGoods);
        }

        return $goods;
    }

    public function getAvailableSkusAttribute()
    {
        if (!$this->goods) return collect();

        $warehouseId = $this->warehouse_id;
        $goodsId = $this->goods_id;
        $params = ['warehouse_id' => $warehouseId, 'goods_id' => $goodsId];
        $skus = app(GoodsSkuService::class)->getSkus($params);
        // 库存>0 的 SKU

        // 当前选中SKU不在列表 → 合并
        $currentSku = $this->sku;
        if ($currentSku && !$skus->contains('id', $currentSku->id)) {
            $skus->prepend($currentSku);
        }

        return $skus;
    }

    public function getStockInfoAttribute()
    {
        $skuId = $this->sku_id;
        $warehouseId = $this->warehouse_id;
        $params = ['sku_id' => $skuId, 'warehouse_id' => $warehouseId];
        $stockInfo = app(GoodsSkuStockService::class)->getStockInfo($params);
        return $stockInfo;
    }

    // 关联入库总单
    public function outbound()
    {
        return $this->belongsTo(Outbound::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function craftMethod()
    {
        return $this->belongsTo(CraftMethod::class);
    }

}

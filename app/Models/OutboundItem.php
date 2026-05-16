<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
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
}

<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use App\Models\Traits\FormatTimeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InboundItem extends Base
{
    use SoftDeletes , FormatTimeTrait;
    protected $fillable = ['inbound_id', 'order_item_id', 'goods_id', 'sku_id','currency_id' ,'quantity', 'price', 'amount', 'status', 'remark',];
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

    public function getInboundAtDateAttribute()
    {
        if (!array_key_exists('inbound_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['inbound_at']
            ? Carbon::parse($this->attributes['inbound_at'])->format('Y-m-d')
            : '';
    }
    // 关联入库总单
    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}

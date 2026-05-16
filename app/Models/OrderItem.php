<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\MockObject\Api;
use Illuminate\Support\Facades\Auth;

class OrderItem extends Model
{
    use SoftDeletes,FormatTimeTrait;
    protected $fillable = ['order_id','goods_id','sku_id','color_card','number','received_quantity','unit_id',
        'currency_id','price','status'];
    protected $dates = ['deleted_at'];

    public function getMoneyAttribute()
    {
        return bcmul($this->number,$this->price,2);
    }

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function goodsSkus()
    {
        return $this->belongsTo(Sku::class,'sku_id');
    }
}

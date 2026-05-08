<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inbound extends Base
{
    use SoftDeletes , FormatTimeTrait;

    protected $fillable = ['department_id', 'customer_id', 'supplier_id', 'warehouse_id', 'inbound_code', 'status',
        'batch_no', 'created_user_id', 'updated_user_id', 'inbound_at',
    ];

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('amount');
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

     public function items()
     {
         return $this->hasMany(InboundItem::class);
     }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }


}

<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use App\Models\Traits\FormatTimeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outbound extends Base
{

    use SoftDeletes,FormatTimeTrait;

    protected $fillable = ['department_id', 'customer_id', 'clearance_id', 'payment_id','tape','seal_container_no' ,
        'outbound_code', 'status', 'created_user_id', 'updated_user_id', 'outbound_at'
    ];
    protected $dates = ['deleted_at'];


    static public function booted()
    {
        static::creating(function ($model) {
            $model->created_user_id = auth()->id();
            $model->updated_user_id = auth()->id();
        });
        static::updating(function ($model) {
            $model->updated_user_id = auth()->id();
        });

        static::addGlobalScope(new DepartmentScope);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('amount');
    }

    public function getOutboundAtDateAttribute()
    {
        if (!array_key_exists('outbound_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['outbound_at']
            ? Carbon::parse($this->attributes['outbound_at'])->format('Y-m-d')
            : '';
    }

    public function items()
    {
        return $this->hasMany(OutboundItem::class);
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

    public function clearance()
    {
        return $this->belongsTo(Clearance::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

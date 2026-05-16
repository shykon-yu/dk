<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use App\Models\Traits\FormatTimeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Base
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->appends = array_merge($this->appends, [
            'ordered_at_date',
            'delivery_at_date',
        ]);
    }
    use SoftDeletes , FormatTimeTrait;
    protected $fillable = ['department_id','customer_id','supplier_id','order_code','status','excel_id','ordered_at','delivery_at',
        'comment','is_star','status_remark','created_user_id','updated_user_id'];
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

    //总金额
    public function getTotalAmountAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total = bcadd($total, bcmul($item->price, $item->number, 2), 2);
        }
        return $total;
    }

    //总数量
    public function getTotalNumberAttribute()
    {
        return $this->items->sum('number');
    }

    //总收货
    public function getTotalReceivedQuantityAttribute()
    {
        return $this->items->sum('received_quantity');
    }

    public function getOrderedAtDateAttribute()
    {
        if (!array_key_exists('ordered_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['ordered_at']
            ? Carbon::parse($this->attributes['ordered_at'])->format('Y-m-d')
            : '';
    }

    public function getDeliveryAtDateAttribute()
    {
        if (!array_key_exists('delivery_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['delivery_at']
            ? Carbon::parse($this->attributes['delivery_at'])->format('Y-m-d')
            : '';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function excel()
    {
        return $this->hasOne(OrderExcel::class);
    }
}

<?php

namespace App\Models;

use App\Models\Scopes\DepartmentScope;
use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Base
{
    use SoftDeletes , FormatTimeTrait;
    protected $fillable = ['department_id','customer_id','supplier_id','name','code','customer_sku','brand_logo',
        'category_id','season_id','status','is_star','main_image','thumb_image','remark','created_user_id','updated_user_id',
        'old_id'];
    protected $dates = ['deleted_at'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_user_id = auth()->id();
            $model->updated_user_id = auth()->id();
            if (empty($model->code)) {
                $model->code = self::generateGoodsCode();
            }
        });

        static::updating(function ($model) {
            $model->updated_user_id = auth()->id();
        });

        static::addGlobalScope(new DepartmentScope);
    }

    //生成内部code
    public static function generateGoodsCode()
    {
        $year = date('y');
        $prefix = $year . 'G';
        $lastCode = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->value('code');

        if ($lastCode) {
            $num = intval(substr($lastCode, 3)) + 1;
        } else {
            $num = 1;
        }
        $code = $prefix . str_pad($num, 6, '0', STR_PAD_LEFT);
        return $code;
    }

    //获取成分组合名称
    public function getComponentTextAttribute()
    {
        return $this->components->map(function($item) {
            return $item->name . ' ' . format_decimal($item->pivot->percent) . '%';
        })->implode(' ');
    }

    //获取成分组合英文名称
    public function getComponentEnTextAttribute()
    {
        return $this->components->map(function($item) {
            return $item->name_en . ' ' . format_decimal($item->pivot->percent) . '%';
        })->implode(' ');
    }

    //获取成分组合韩文名称
    public function getComponentKrTextAttribute()
    {
        return $this->components->map(function($item) {
            return $item->name_kr . ' ' . format_decimal($item->pivot->percent) . '%';
        })->implode(' ');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_user_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(GoodsCategory::class);
    }

    public function season()
    {
        return $this->belongsTo(GoodsSeason::class);
    }

    public function components()
    {
        return $this->belongsToMany(GoodsComponent::class,'goods_goods_component')->withPivot('percent');
    }

    public function skus(){
        return $this->hasMany(Sku::class);
    }

    public function stocks()
    {
        return $this->hasMany(GoodsSkuStock::class,'goods_id','id');
    }

    public function orderItems()
    {
        return $this->hasMany( orderItem::class, 'goods_id','id' );
    }
}

<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Base
{
    use HasFactory ,SoftDeletes , FormatTimeTrait;
    protected $fillable = ['name','supplier_category_id','contact','phone','email','address','remark','status','sort','remark','created_user_id','updated_user_id'];
    protected $dates = ['deleted_at'];

    public function creator()
    {
        return $this->belongsTo(User::class,'created_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_user_id');
    }

    public static function booted()
    {
        static::creating(function ($model) {
            $model->created_user_id = auth()->id();
            $model->updated_user_id = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_user_id = auth()->id();
        });
    }

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

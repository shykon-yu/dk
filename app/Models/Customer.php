<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Base
{
    use HasFactory , FormatTimeTrait , SoftDeletes;
    protected $fillable = ['name','name_kr','parent_id','brand_logo','sku_prefix','clearance_id',
        'payment_id','contact','email','phone','address','department_id','status','remark','created_user_id','updated_user_id'];
    protected $dates = ['deleted_at'];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_user_id');
    }

    public function clearance()
    {
        return $this->belongsTo(Clearance::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function children()
    {
        return $this->hasMany(self::class);
    }
}

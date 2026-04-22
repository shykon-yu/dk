<?php

namespace App\Models;

//use App\Models\Traits\FormatTimeTrait;
use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Base
{
    use HasFactory , FormatTimeTrait , SoftDeletes;
    protected $fillable = ['name','department_id','status'];
    protected $dates = ['deleted_at'];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function stocks()
    {
        return $this->hasMany(GoodsSkuStock::class,'warehouse_id','id');
    }
}

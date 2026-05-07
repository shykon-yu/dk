<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderExcel extends Base
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['order_id','name','file_path','created_at','updated_at','deleted_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\MockObject\Api;

class OrderItem extends Model
{
    use SoftDeletes,FormatTimeTrait;
    protected $fillable = ['order_id','goods_id','sku_id','color_card','number','received_quantity','unit_id',
        'currency_id','price','status'];
    protected $dates = ['deleted_at'];
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

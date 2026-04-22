<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// 模型：单数
class GoodsSkuStock extends Base
{
    use FormatTimeTrait;
    protected $fillable = [
        'goods_id','sku_id', 'warehouse_id', 'stock', 'lock_stock', 'available_stock',
    ];
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    public function sku()
    {
        return $this->belongsTo(Sku::class, 'sku_id');
    }
}

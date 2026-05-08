<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundItem extends Model
{
    use SoftDeletes , FormatTimeTrait;
    protected $fillable = ['inbound_id', 'order_id', 'goods_id', 'sku_id', 'quantity', 'price', 'amount', 'status', 'remark',];

    // 关联入库总单
    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}

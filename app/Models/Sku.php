<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sku extends Base
{
    use HasFactory , SoftDeletes , FormatTimeTrait;
    protected $fillable = ['id','goods_id','color','size','stock','sell_price','sell_price2','cost_price','cost_price2',
        'process_price','process_step2_price','status','sell_currency_id','cost_currency_id'];
    protected $dates = ['deleted_at'];

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->cost_all_price = $model->cost_price + $model->process_price + $model->process_step2_price;
            $model->cost_all_price2 = $model->cost_price2 + $model->process_price + $model->process_step2_price;
        });
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class,'goods_id');
    }
    public function stocks()
    {
        return $this->hasMany(GoodsSkuStock::class,'sku_id','id');
    }
}

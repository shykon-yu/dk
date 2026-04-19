<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsComponent extends Base
{
    use HasFactory , FormatTimeTrait , SoftDeletes;
    protected $fillable = ['name','name_en','name_kr','sort','status'];
    protected $dates = ['deleted_at'];

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }
}

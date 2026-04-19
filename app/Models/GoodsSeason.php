<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsSeason extends Base
{
    use HasFactory , SoftDeletes , FormatTimeTrait;
    protected $fillable = [
      'name','year','season','status'
    ];

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }
}

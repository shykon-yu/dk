<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCategory extends Model
{
    use HasFactory , FormatTimeTrait , SoftDeletes;
    protected $fillable = ['name','parent_id','level','status','sort'];
    protected $dates = ['deleted_at'];

    public function parent()
    {
        return $this->belongsTo(self::class,'parent_id');
    }
    public function children()
    {
        return $this->hasMany(self::class,'parent_id');
    }
}

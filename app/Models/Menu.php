<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory , FormatTimeTrait , SoftDeletes ;
    protected $fillable = ['title','status','route','parent_id','sort','permission'];
    protected $dates = ['deleted_at'];
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('status',self::STATUS_ENABLE)
            ->orderBy('sort');
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}

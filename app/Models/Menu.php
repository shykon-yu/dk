<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    use HasFactory , FormatTimeTrait;
    protected $fillable = ['title','status','route','parent_id','sort','permission'];
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = ['title','status','route','url','parent_id','order'];
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('status',self::STATUS_ENABLE)
            ->orderBy('order');
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}

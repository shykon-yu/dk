<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name','status'];
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    public function users()
    {
        return $this->belongsToMany(User::class,'user_department','department_id','user_id');
    }
}

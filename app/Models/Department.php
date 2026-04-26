<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Base
{
    use HasFactory , SoftDeletes , FormatTimeTrait;
    protected $fillable = ['name','status'];
    protected $dates = ['deleted_at'];
    public function users()
    {
        return $this->belongsToMany(User::class,'user_department');
    }

    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function Warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function goods()
    {
        return $this->hasMany(Goods::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clearance extends Base
{
    use HasFactory ,SoftDeletes , FormatTimeTrait;
    protected $fillable = ['name'];
    protected $dates = ['deleted_at'];
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}

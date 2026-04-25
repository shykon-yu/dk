<?php

namespace App\Models;

use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clearance extends Base
{
    use  SoftDeletes , FormatTimeTrait;
    protected $fillable = ['name','name_kr','sort','status'];
    protected $dates = ['deleted_at'];
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}

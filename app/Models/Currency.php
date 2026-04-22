<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'code', 'symbol','rate', 'is_base', 'status', 'sort', 'decimal_sep', 'thousand_sep', 'decimal_digits',
    ];
}

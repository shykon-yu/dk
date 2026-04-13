<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use App\Models\Traits\FormatTimeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SoftDeletes , FormatTimeTrait;
    protected $dates = ['deleted_at'];
    protected static function booted()
    {
        parent::booted();
    }
}

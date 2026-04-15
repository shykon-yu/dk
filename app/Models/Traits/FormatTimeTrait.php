<?php
namespace App\Models\Traits;
use Carbon\Carbon;
trait FormatTimeTrait{
    protected $appends = [
        'created_at',
        'updated_at',
        'created_at_date',
        'updated_at_date',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getDeletedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function getCreatedAtDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d');
    }

    public function getUpdatedAtDateAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d');
    }
}

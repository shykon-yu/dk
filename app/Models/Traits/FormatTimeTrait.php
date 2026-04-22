<?php
namespace App\Models\Traits;
use Carbon\Carbon;
trait FormatTimeTrait{
    protected $appends = ['created_at_date','updated_at_date'];
    public function getCreatedAtDateAttribute()
    {
        if (!array_key_exists('created_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['created_at']
            ? Carbon::parse($this->attributes['created_at'])->format('Y-m-d')
            : '';
    }

    public function getUpdatedAtDateAttribute()
    {
        if (!array_key_exists('updated_at', $this->attributes)) {
            return '';
        }

        return $this->attributes['updated_at']
            ? Carbon::parse($this->attributes['updated_at'])->format('Y-m-d')
            : '';
    }
}

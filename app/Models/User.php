<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles , SoftDeletes;
    protected $fillable = [
        'name', 'username', 'email', 'password', 'phone_number', 'status', 'section_id', 'open_id'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getDepartmentsNameAttribute()
    {
        return $this->departments->map(function($item){
            return $item->name;
        })->implode(',');
    }

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope());
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_department');
    }

    public function getDeptIdArray()
    {
        return $this->departments()->pluck('departments.id')->toArray();
    }
}

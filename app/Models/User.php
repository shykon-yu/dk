<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\DepartmentScope;
use App\Models\Scopes\DepartmentSelectScope;
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

    //部门id是否在权限部门之内
    public function hasDepartment($departmentId)
    {
        if ($this->id === 1) return true;

        // 单部门
        //return $this->department_id == $departmentId;

        // 多部门
        return in_array($departmentId, $this->departments->pluck('id')->all());
    }

    protected static function booted()
    {
        //static::addGlobalScope(new ActiveScope());
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

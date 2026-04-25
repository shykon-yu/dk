<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
class DepartmentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::user()->id === 1) {
            return;
        }
        // 普通用户只看自己部门
        $deptIds = Auth::user()->departments->pluck('id')->toArray();
        $builder->whereIn($model->getTable() . '.department_id', $deptIds);
    }
}

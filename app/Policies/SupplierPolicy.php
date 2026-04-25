<?php

namespace App\Policies;

use App\Models\User;
use App\Models\supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability , Supplier $supplier ){
        //获取该用户最高级层级数
        $currentLevel = $user->roles->sortBy('level')->first()?->level ?? 999;
        $targetLevel = $supplier->creator->roles->sortBy('level')->first()?->level ?? 999;
        //层级数1以上角色，判断层级，如果层级数大于作者的层级数则不能修改
        if ($currentLevel >= $targetLevel && $currentLevel > 1 && $user->id != $supplier->created_user_id) {
            return false;
        }
        return true;
    }

    public function update(User $user, Role $role)
    {

    }

    public function delete(User $user, Role $role)
    {

    }
}

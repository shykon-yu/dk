<?php

namespace App\Policies;

use App\Models\User;
use App\Models\role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    public function before(User $user, $ability , Role $role ){
//        if (!in_array($ability, ['update', 'delete'])) {
//            return null; // 不控制其他权限
//        }
        //获取该用户最高级层级数
        $currentLevel = $user->roles->sortBy('level')->first()?->level ?? 999;
        $targetLevel = $role->level;
        $currentRoles = $user->roles->pluck('id')->toArray();
        if ($currentLevel >= $targetLevel) {
            return false;
        }
        if( in_array($role->id,$currentRoles) ) {
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

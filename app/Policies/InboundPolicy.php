<?php

namespace App\Policies;

use App\Models\Inbound;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InboundPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability , Inbound $inbound ){
        if (!in_array($ability, ['update', 'delete'])) {
            return null;
        }

        //获取该用户最高级层级数
        $currentLevel = $user->roles->sortBy('level')->first()?->level ?? 999;
        $targetLevel = $inbound->creator->roles->sortBy('level')->first()?->level ?? 999;
        //层级数1以上角色，判断层级，如果层级数大于作者的层级数则不能修改
        if ($currentLevel >= $targetLevel && $currentLevel > 1 && $user->id != $inbound->created_user_id) {
            return false;
        }
        //已经开始生产入库，就不能进行更改了
        if($inbound->status != 0) {
            return false;
        }

        return true;
    }

    public function update(User $user, Inbound $inbound)
    {

    }

    public function delete(User $user, Inbound $inbound)
    {

    }
}

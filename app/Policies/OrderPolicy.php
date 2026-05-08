<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability , Order $order ){
        if (!in_array($ability, ['update', 'delete'])) {
            return null;
        }

        //获取该用户最高级层级数
        $currentLevel = $user->roles->sortBy('level')->first()?->level ?? 999;
        $targetLevel = $order->creator->roles->sortBy('level')->first()?->level ?? 999;
        //层级数1以上角色，判断层级，如果层级数大于作者的层级数则不能修改
        if ($currentLevel >= $targetLevel && $currentLevel > 1 && $user->id != $order->created_user_id) {
            return false;
        }
        //已经开始生产入库，就不能进行更改了
        if($order->status != 0) {
            return false;
        }

        return true;
    }

    public function update(User $user, Order $order)
    {

    }

    public function delete(User $user, Order $order)
    {

    }

    public function reorder(User $user, Order $order)
    {
        if (!$user->hasDepartment($order->department_id)) {
            return false;
        }
        return true;
    }
}

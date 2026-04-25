<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    public function update(User $user, User $model)
    {
        $currentLevel = $user->roles->sortBy('level')->first()?->level ?? 999;
        if( $user->id == 1 || $currentLevel <=1 ){
            return true;
        }
        if( $user->id != $model->id ){
            return false;
        }
        return true;
    }

    public function delete(User $user, User $model)
    {
        if ($user->id === $model->id) {
            return false;
        }
        if ($model->id === 1) {
            return false;
        }
        return true;
    }
}

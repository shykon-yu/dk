<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability)
    {
//        if ($user->id === 1) {
//            return true;
//        }
    }
    public function viewAny(User $user)
    {
        //
    }


    public function view(User $user, User $model)
    {
        //
    }


    public function create(User $user)
    {
        //
    }

    public function update(User $user, User $model)
    {
        //
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


    public function restore(User $user, User $model)
    {
        //
    }


    public function forceDelete(User $user, User $model)
    {
        //
    }
}

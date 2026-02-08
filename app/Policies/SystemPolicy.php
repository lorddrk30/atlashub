<?php

namespace App\Policies;

use App\Models\System;
use App\Models\User;

class SystemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('system.manage');
    }

    public function view(User $user, System $system): bool
    {
        return $user->can('system.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('system.manage');
    }

    public function update(User $user, System $system): bool
    {
        return $user->can('system.manage');
    }

    public function delete(User $user, System $system): bool
    {
        return $user->can('system.manage');
    }
}

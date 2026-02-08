<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;

class ModulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('module.manage');
    }

    public function view(User $user, Module $module): bool
    {
        return $user->can('module.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('module.manage');
    }

    public function update(User $user, Module $module): bool
    {
        return $user->can('module.manage');
    }

    public function delete(User $user, Module $module): bool
    {
        return $user->can('module.manage');
    }
}

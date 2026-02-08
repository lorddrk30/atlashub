<?php

namespace App\Policies;

use App\Models\OrganizationSetting;
use App\Models\User;

class OrganizationSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('organization.manage');
    }

    public function view(User $user, OrganizationSetting $organizationSetting): bool
    {
        return $user->can('organization.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('organization.manage');
    }

    public function update(User $user, OrganizationSetting $organizationSetting): bool
    {
        return $user->can('organization.manage');
    }

    public function delete(User $user, OrganizationSetting $organizationSetting): bool
    {
        return false;
    }
}


<?php

namespace App\Policies;

use App\Models\Artefact;
use App\Models\User;

class ArtefactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('artefact.manage');
    }

    public function view(User $user, Artefact $artefact): bool
    {
        return $user->can('artefact.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('artefact.manage');
    }

    public function update(User $user, Artefact $artefact): bool
    {
        return $user->can('artefact.manage');
    }

    public function delete(User $user, Artefact $artefact): bool
    {
        return $user->can('artefact.manage');
    }
}

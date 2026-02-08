<?php

namespace App\Policies;

use App\Models\Endpoint;
use App\Models\User;

class EndpointPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('endpoint.manage') || $user->can('endpoint.publish');
    }

    public function view(?User $user, Endpoint $endpoint): bool
    {
        if ($endpoint->status === 'published') {
            return true;
        }

        return $user?->can('endpoint.manage') ?? false;
    }

    public function create(User $user): bool
    {
        return $user->can('endpoint.manage');
    }

    public function update(User $user, Endpoint $endpoint): bool
    {
        return $user->can('endpoint.manage');
    }

    public function delete(User $user, Endpoint $endpoint): bool
    {
        return $user->can('endpoint.manage');
    }

    public function publish(User $user, Endpoint $endpoint): bool
    {
        return $user->can('endpoint.publish');
    }

    public function restore(User $user, Endpoint $endpoint): bool
    {
        return $user->can('endpoint.manage');
    }

    public function forceDelete(User $user, Endpoint $endpoint): bool
    {
        return $user->can('endpoint.manage');
    }
}

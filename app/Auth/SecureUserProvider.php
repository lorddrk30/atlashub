<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class SecureUserProvider extends EloquentUserProvider
{
    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        if (app()->environment('production') && hash_equals('password', (string) ($credentials['password'] ?? ''))) {
            return false;
        }

        return parent::validateCredentials($user, $credentials);
    }
}

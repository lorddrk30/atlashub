<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('refuses to seed predictable demonstration accounts in production', function (): void {
    app()->detectEnvironment(fn () => 'production');

    expect(fn () => (new DatabaseSeeder)->run())->toThrow(RuntimeException::class);
    expect(User::query()->count())->toBe(0);
});

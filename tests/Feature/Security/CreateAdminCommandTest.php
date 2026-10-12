<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('bootstraps a production administrator without demo credentials', function (): void {
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('atlashub:create-admin', ['email' => 'owner@example.test', 'name' => 'Site Owner'])
        ->expectsQuestion('Admin password', 'Strong-Passphrase-42!')
        ->expectsQuestion('Confirm admin password', 'Strong-Passphrase-42!')
        ->assertSuccessful();

    $admin = User::query()->sole();
    expect($admin->email)->toBe('owner@example.test');
    expect(Hash::check('Strong-Passphrase-42!', $admin->password))->toBeTrue();
    expect($admin->hasRole('admin'))->toBeTrue();
    expect($admin->can('user.manage'))->toBeTrue();
    expect(Role::query()->where('name', 'admin')->exists())->toBeTrue();
});

it('is a no-op for an existing admin and refuses to promote an existing non-admin', function (): void {
    $role = Role::findOrCreate('admin', 'web');
    $admin = User::factory()->create(['email' => 'admin@example.test', 'password' => 'Existing-Passphrase-42!']);
    $admin->assignRole($role);
    $oldHash = $admin->password;

    $this->artisan('atlashub:create-admin', ['email' => $admin->email, 'name' => 'Changed Name'])
        ->assertSuccessful();
    expect($admin->fresh()->name)->not->toBe('Changed Name');
    expect($admin->fresh()->password)->toBe($oldHash);

    $viewer = User::factory()->create(['email' => 'viewer@example.test']);
    $this->artisan('atlashub:create-admin', ['email' => $viewer->email, 'name' => 'Viewer'])
        ->assertFailed();
    expect($viewer->fresh()->hasRole('admin'))->toBeFalse();
    expect(User::query()->count())->toBe(2);
});

it('rejects weak or mismatched passwords without creating an account', function (): void {
    $this->artisan('atlashub:create-admin', ['email' => 'weak@example.test', 'name' => 'Weak'])
        ->expectsQuestion('Admin password', 'password')
        ->expectsQuestion('Confirm admin password', 'password')
        ->assertFailed();

    $this->artisan('atlashub:create-admin', ['email' => 'mismatch@example.test', 'name' => 'Mismatch'])
        ->expectsQuestion('Admin password', 'Strong-Passphrase-42!')
        ->expectsQuestion('Confirm admin password', 'Different-Passphrase-42!')
        ->assertFailed();

    expect(User::query()->count())->toBe(0);
});

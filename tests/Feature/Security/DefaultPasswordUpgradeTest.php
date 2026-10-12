<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Livewire;

it('rejects known demonstration passwords in production for both portal and Filament providers', function (): void {
    $legacy = User::factory()->create(['email' => 'legacy@example.test', 'password' => 'password']);
    $safe = User::factory()->create(['email' => 'safe@example.test', 'password' => 'Safe-Passphrase-42!']);
    app()->detectEnvironment(fn () => 'production');
    $token = str_repeat('x', 40);

    $this->withSession(['_token' => $token])
        ->post('/login', ['_token' => $token, 'email' => $legacy->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
    expect(Auth::guard('web')->getProvider()->validateCredentials($legacy, ['password' => 'password']))->toBeFalse();
    expect(Auth::guard('web')->getProvider()->validateCredentials($safe, ['password' => 'Safe-Passphrase-42!']))->toBeTrue();
});

it('audits and securely rotates legacy passwords without exposing plaintext', function (): void {
    $user = User::factory()->create(['email' => 'legacy@example.test', 'password' => 'password']);
    $user->createToken('old-session');

    $this->artisan('atlashub:audit-default-passwords')->assertFailed();
    $this->artisan('atlashub:rotate-password', ['email' => $user->email])
        ->expectsQuestion('New password', 'Fresh-Passphrase-42!')
        ->expectsQuestion('Confirm new password', 'Fresh-Passphrase-42!')
        ->assertSuccessful();

    expect(Hash::check('password', $user->fresh()->password))->toBeFalse();
    expect(Hash::check('Fresh-Passphrase-42!', $user->fresh()->password))->toBeTrue();
    expect(PersonalAccessToken::query()->count())->toBe(0);
    $this->artisan('atlashub:audit-default-passwords')->assertSuccessful();
});

it('rejects the demonstration password when an account changes or resets its password', function (): void {
    $user = User::factory()->create(['password' => 'Safe-Passphrase-42!']);
    $originalHash = $user->password;
    app()->detectEnvironment(fn () => 'production');
    $token = str_repeat('y', 40);

    $this->withSession(['_token' => $token])->actingAs($user)->put('/password', [
        '_token' => $token,
        'current_password' => 'Safe-Passphrase-42!',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('password');

    auth()->logout();
    Notification::fake();
    $this->post('/forgot-password', ['_token' => $token, 'email' => $user->email]);
    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $token): bool {
        $this->post('/reset-password', [
            '_token' => $token,
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        return true;
    });

    expect($user->fresh()->password)->toBe($originalHash);
});

it('rejects the demonstration password when an admin creates a user', function (): void {
    app(AccessControlSeeder::class)->run();
    $admin = User::factory()->create(['password' => 'Safe-Passphrase-42!']);
    $admin->assignRole('admin');
    app()->detectEnvironment(fn () => 'production');

    Livewire::actingAs($admin)
        ->test(CreateUser::class)
        ->fillForm([
            'name' => 'New User',
            'email' => 'new-user@example.test',
            'password' => 'password',
        ])
        ->call('create')
        ->assertHasFormErrors(['password']);

    $this->assertDatabaseMissing('users', ['email' => 'new-user@example.test']);
});

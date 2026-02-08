<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

it('redirects guest users to admin login from backoffice entry', function (): void {
    $response = $this->get('/backoffice');

    $response->assertRedirect('/admin/login');
});

it('redirects admin users to filament panel from backoffice entry', function (): void {
    $role = Role::query()->create(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/backoffice');

    $response->assertRedirect('/admin');
});

it('redirects viewer users to friendly no-access page from backoffice entry', function (): void {
    $role = Role::query()->create(['name' => 'viewer', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/backoffice');

    $response->assertRedirect(route('backoffice.forbidden'));
});

it('renders a friendly backoffice forbidden page for viewer users', function (): void {
    $role = Role::query()->create(['name' => 'viewer', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get(route('backoffice.forbidden'));

    $response->assertOk();
    $response->assertSee('Esta cuenta no tiene acceso', false);
});


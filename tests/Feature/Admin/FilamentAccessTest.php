<?php

use App\Models\User;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows admin role to access filament panel', function (): void {
    $role = Role::query()->create(['name' => 'admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
});

it('denies viewer role to access filament panel', function (): void {
    $role = Role::query()->create(['name' => 'viewer', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
});

it('renders endpoint detail page when endpoint has nested json fields', function (): void {
    $role = Role::query()->create(['name' => 'admin', 'guard_name' => 'web']);
    $permission = Permission::query()->create(['name' => 'endpoint.manage', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    $system = System::factory()->create();
    $module = Module::factory()->create(['system_id' => $system->id]);
    $endpoint = Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'published',
        'parameters' => [
            ['name' => 'store_id', 'in' => 'query', 'required' => true, 'type' => 'string'],
        ],
        'request_example' => [
            'items' => [
                ['sku' => 'ESPRESSO', 'qty' => 1],
            ],
        ],
        'response_example' => [
            'result' => [
                'order_id' => 'ORDER-1',
                'meta' => ['source' => 'test'],
            ],
        ],
        'urls' => [
            'prod' => 'https://api.example.local/orders',
            'uat' => 'https://api-uat.example.local/orders',
        ],
    ]);

    $response = $this->actingAs($user)->get("/admin/endpoints/{$endpoint->id}");

    $response->assertSuccessful();
});

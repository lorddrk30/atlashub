<?php

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns grouped catalog results', function (): void {
    $user = User::factory()->create();
    $system = System::factory()->create(['name' => 'Coffee Experience', 'slug' => 'coffee-experience']);
    $module = Module::factory()->create([
        'system_id' => $system->id,
        'name' => 'Coffee Menu Catalog',
        'slug' => 'menu-catalog',
    ]);

    $endpoint = Endpoint::factory()->create([
        'module_id' => $module->id,
        'name' => 'List coffee menu items',
        'method' => 'GET',
        'path' => '/api/v1/menu/items',
        'description' => 'Lista bebidas y productos del menu',
        'status' => 'published',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    Artefact::factory()->create([
        'system_id' => $system->id,
        'module_id' => $module->id,
        'endpoint_id' => $endpoint->id,
        'type' => 'swagger',
        'title' => 'Swagger Coffee Menu',
        'url' => 'https://docs.example.local/swagger',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $response = $this->getJson('/api/v1/search?q=coffee');

    $response
        ->assertOk()
        ->assertJsonPath('counts.systems', 1)
        ->assertJsonPath('counts.modules', 1)
        ->assertJsonPath('counts.endpoints', 1)
        ->assertJsonPath('counts.artefacts', 1)
        ->assertJsonPath('grouped.endpoints.0.public_id', $endpoint->public_id)
        ->assertJsonPath('grouped.endpoints.0.path', '/api/v1/menu/items');
});

it('returns endpoint detail by public id', function (): void {
    $system = System::factory()->create(['name' => 'Store Operations', 'slug' => 'store-operations']);
    $module = Module::factory()->create(['system_id' => $system->id]);

    $endpoint = Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'published',
        'method' => 'GET',
        'path' => '/api/v1/shifts/{shift_id}',
    ]);

    $response = $this->getJson('/api/v1/endpoints/'.$endpoint->public_id);

    $response
        ->assertOk()
        ->assertJsonPath('item.public_id', $endpoint->public_id)
        ->assertJsonPath('item.path', '/api/v1/shifts/{shift_id}')
        ->assertJsonPath('item.module.system.id', $system->id);
});

it('returns 404 for unpublished endpoint detail', function (): void {
    $system = System::factory()->create();
    $module = Module::factory()->create(['system_id' => $system->id]);

    $endpoint = Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'draft',
    ]);

    $this->getJson('/api/v1/endpoints/'.$endpoint->public_id)
        ->assertNotFound();
});

it('returns filters catalog', function (): void {
    $system = System::factory()->create(['name' => 'Supply Chain', 'slug' => 'supply-chain']);
    Module::factory()->create(['name' => 'Supplier Orders', 'system_id' => $system->id]);

    $response = $this->getJson('/api/v1/filters');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'systems',
            'modules',
            'methods',
            'authentication_types',
            'artefact_types',
        ]);
});

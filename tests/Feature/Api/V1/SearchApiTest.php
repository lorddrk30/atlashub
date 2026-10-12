<?php

use App\Models\Artefact;
use App\Models\Document;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('returns grouped catalog results', function (): void {
    $user = User::factory()->create();
    $system = System::factory()->create([
        'name' => 'Coffee Experience',
        'slug' => 'coffee-experience',
        'prod_server' => 'prod-coffee-api-01.internal.local',
        'dev_server' => 'dev-coffee-api-01.internal.local',
        'internal_url' => 'https://coffee.internal.local',
        'public_url' => 'https://coffee.example.com',
        'responsibles' => ['Equipo CX', 'Arquitectura'],
        'user_areas' => ['Operacion', 'Comercial'],
        'repository_url' => 'https://github.com/coffee/coffee-experience',
        'home_preview_url' => 'https://placehold.co/1200x720/0b1220/22d3ee?text=Coffee+Home',
    ]);
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

    Document::query()->create([
        'system_id' => $system->id,
        'module_id' => $module->id,
        'endpoint_id' => $endpoint->id,
        'title' => 'Manual operativo coffee',
        'description' => 'Procedimiento para alta de productos.',
        'type' => 'manual',
        'file_path' => 'documents/'.$system->slug.'/manual-operativo.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
        'uploaded_by' => $user->id,
    ]);

    $response = $this->getJson('/api/v1/search?q=coffee');

    $response
        ->assertOk()
        ->assertJsonPath('counts.systems', 1)
        ->assertJsonPath('counts.modules', 1)
        ->assertJsonPath('counts.endpoints', 1)
        ->assertJsonPath('counts.artefacts', 1)
        ->assertJsonPath('counts.documents', 1)
        ->assertJsonPath('grouped.systems.0.prod_server', 'prod-coffee-api-01.internal.local')
        ->assertJsonPath('grouped.systems.0.repository_url', 'https://github.com/coffee/coffee-experience')
        ->assertJsonPath('grouped.documents.0.title', 'Manual operativo coffee')
        ->assertJsonPath('grouped.endpoints.0.public_id', $endpoint->public_id)
        ->assertJsonPath('grouped.endpoints.0.path', '/api/v1/menu/items');
});

it('searches systems by infrastructure metadata', function (): void {
    System::factory()->create([
        'name' => 'Payments',
        'slug' => 'payments',
        'description' => 'Core de pagos',
        'repository_url' => 'https://github.com/core/payments',
        'home_preview_url' => 'systems/previews/payments-home.png',
    ]);

    $response = $this->getJson('/api/v1/search?q=github.com/core/payments');

    $response
        ->assertOk()
        ->assertJsonPath('counts.systems', 1)
        ->assertJsonPath('grouped.systems.0.slug', 'payments')
        ->assertJsonPath('grouped.systems.0.home_preview_url', 'http://127.0.0.1:8000/storage/systems/previews/payments-home.png');
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

it('returns only published endpoints from the selected private system', function (): void {
    $selected = System::factory()->create([
        'name' => 'Inventory',
        'prod_server' => 'inventory-prod.internal',
        'prod_server_ip' => '10.10.0.7',
        'uat_server' => 'inventory-uat.internal',
        'uat_server_ip' => '10.20.0.7',
        'responsibles' => ['Platform Team'],
    ]);
    $other = System::factory()->create();
    $selectedModule = Module::factory()->create(['system_id' => $selected->id]);
    $otherModule = Module::factory()->create(['system_id' => $other->id]);
    $published = Endpoint::factory()->create(['module_id' => $selectedModule->id, 'status' => 'published']);
    $draft = Endpoint::factory()->create(['module_id' => $selectedModule->id, 'status' => 'draft']);
    $outside = Endpoint::factory()->create(['module_id' => $otherModule->id, 'status' => 'published']);

    $this->getJson('/api/v1/search?system_id='.$selected->id.'&per_category=50')
        ->assertOk()
        ->assertJsonPath('grouped.systems.0.id', $selected->id)
        ->assertJsonPath('grouped.systems.0.prod_server_ip', '10.10.0.7')
        ->assertJsonPath('grouped.systems.0.uat_server_ip', '10.20.0.7')
        ->assertJsonPath('grouped.systems.0.responsibles.0', 'Platform Team')
        ->assertJsonCount(1, 'grouped.endpoints')
        ->assertJsonPath('grouped.endpoints.0.public_id', $published->public_id)
        ->assertDontSee($draft->public_id)
        ->assertDontSee($outside->public_id);

    $this->getJson('/api/v1/search?system_id='.$selected->id.'&module_id='.$selectedModule->id.'&per_category=50')
        ->assertOk()
        ->assertJsonCount(1, 'grouped.endpoints');
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
            'document_types',
        ]);
});

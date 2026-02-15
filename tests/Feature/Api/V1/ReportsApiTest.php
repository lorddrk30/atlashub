<?php

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns report summary with kpis, charts and tables', function (): void {
    $user = User::factory()->create();
    $system = System::factory()->create(['name' => 'Cafe Ops', 'slug' => 'cafe-ops']);
    $module = Module::factory()->create([
        'system_id' => $system->id,
        'name' => 'Pedidos',
        'slug' => 'pedidos',
    ]);

    $endpoint = Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'published',
        'method' => 'GET',
        'path' => '/api/v1/orders',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    Artefact::factory()->create([
        'system_id' => $system->id,
        'module_id' => $module->id,
        'endpoint_id' => $endpoint->id,
        'type' => 'swagger',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $response = $this->getJson('/api/v1/reports/summary');

    $response
        ->assertOk()
        ->assertJsonPath('kpis.systems', 1)
        ->assertJsonPath('kpis.modules', 1)
        ->assertJsonPath('kpis.endpoints', 1)
        ->assertJsonPath('kpis.artefacts', 1)
        ->assertJsonStructure([
            'kpis',
            'charts' => [
                'endpoints_by_system',
                'endpoints_by_module',
                'artefacts_by_type',
                'endpoints_by_method',
            ],
            'tables' => [
                'systems',
                'modules',
                'endpoints',
                'artefacts',
            ],
            'filters',
            'filters_applied',
            'generated_at',
        ]);
});

it('applies lifecycle status filters on report summary', function (): void {
    $module = Module::factory()->create();

    Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'published',
        'method' => 'GET',
        'path' => '/api/v1/menu',
    ]);

    Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'archived',
        'method' => 'POST',
        'path' => '/api/v1/menu/archive',
    ]);

    $active = $this->getJson('/api/v1/reports/summary?status=active');
    $archived = $this->getJson('/api/v1/reports/summary?status=archived');

    $active->assertOk()->assertJsonPath('kpis.endpoints', 1);
    $archived->assertOk()->assertJsonPath('kpis.endpoints', 1);
});

it('generates report pdf document', function (): void {
    $module = Module::factory()->create();

    Endpoint::factory()->create([
        'module_id' => $module->id,
        'status' => 'published',
    ]);

    $response = $this->postJson('/api/v1/reports/generate-pdf', [
        'title' => 'Reporte QA',
        'theme' => 'dark',
        'disposition' => 'inline',
    ]);

    $response->assertOk();

    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');
    expect((string) $response->headers->get('content-disposition'))->toContain('inline;');
});

it('includes draft systems in report summary tables', function (): void {
    $draftSystem = System::factory()->create([
        'name' => 'Nomina Interna',
        'slug' => 'nomina-interna',
        'status' => 'draft',
    ]);

    $response = $this->getJson('/api/v1/reports/summary?system_status=draft');

    $response
        ->assertOk()
        ->assertJsonPath('kpis.systems', 1)
        ->assertJsonPath('tables.systems.0.id', $draftSystem->id)
        ->assertJsonPath('tables.systems.0.status', 'draft');
});

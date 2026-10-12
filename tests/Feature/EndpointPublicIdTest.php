<?php

use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('backfills missing public IDs without changing existing endpoint IDs or statuses', function (): void {
    $system = System::factory()->create();
    $module = Module::factory()->create(['system_id' => $system->id]);
    $published = Endpoint::factory()->create(['module_id' => $module->id, 'status' => 'published']);
    $draft = Endpoint::factory()->create(['module_id' => $module->id, 'status' => 'draft']);
    $existing = Endpoint::factory()->create(['module_id' => $module->id, 'status' => 'published']);
    $existingPublicId = $existing->public_id;

    DB::table('endpoints')->whereIn('id', [$published->id, $draft->id])->update(['public_id' => null]);

    $migration = require database_path('migrations/2026_10_11_000001_backfill_endpoint_public_ids.php');
    $migration->up();

    foreach ([$published, $draft] as $endpoint) {
        expect(Str::isUlid($endpoint->fresh()->public_id))->toBeTrue();
    }
    expect($published->fresh()->status)->toBe('published');
    expect($draft->fresh()->status)->toBe('draft');
    expect($existing->fresh()->public_id)->toBe($existingPublicId);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/search?system_id='.$system->id)
        ->assertOk()
        ->assertJsonFragment(['public_id' => $published->fresh()->public_id])
        ->assertDontSee($draft->fresh()->public_id);
    $this->getJson('/api/v1/endpoints/'.$published->fresh()->public_id)->assertOk();
    $this->getJson('/api/v1/endpoints/'.$draft->fresh()->public_id)->assertNotFound();
});

it('creates and repairs demo endpoints with stable public IDs across repeated seeding', function (): void {
    $system = System::factory()->create(['slug' => 'customer-experience']);
    $module = Module::factory()->create(['system_id' => $system->id, 'slug' => 'orders']);
    $existing = Endpoint::factory()->create([
        'module_id' => $module->id,
        'method' => 'POST',
        'path' => '/api/v1/orders',
    ]);
    DB::table('endpoints')->where('id', $existing->id)->update(['public_id' => null]);

    $this->seed(DatabaseSeeder::class);

    $repairedPublicId = $existing->fresh()->public_id;
    expect(Str::isUlid($repairedPublicId))->toBeTrue();
    expect(Endpoint::query()->whereNull('public_id')->count())->toBe(0);

    $this->seed(DatabaseSeeder::class);

    expect($existing->fresh()->public_id)->toBe($repairedPublicId);
    expect(Endpoint::query()->whereNull('public_id')->count())->toBe(0);
});

<?php

use App\Models\OrganizationSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders portal branding from organization settings', function (): void {
    OrganizationSetting::query()->create([
        'name' => 'Cafe Nova',
        'short_name' => 'CN',
        'slug' => 'cafe-nova',
        'tagline' => 'Portal tecnico cafe',
        'description' => 'Portal interno para equipos de Cafe Nova.',
        'logo_url' => null,
        'favicon_url' => null,
        'support_email' => 'tech@cafenova.local',
        'primary_color' => '#22d3ee',
        'secondary_color' => '#34d399',
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Cafe Nova | AtlasHub', false);
    $response->assertSee('"name":"Cafe Nova"', false);
});


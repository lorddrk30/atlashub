<?php

use App\Models\Document;
use App\Models\System;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows only approved public PDFs without exposing their system infrastructure', function (): void {
    $system = System::factory()->create(['prod_server_ip' => '10.22.33.44']);
    $public = Document::factory()->for($system)->create([
        'title' => 'Guía pública',
        'description' => 'Primeros pasos',
        'is_public' => true,
    ]);
    Document::factory()->for($system)->create([
        'title' => 'Manual privado',
        'description' => 'Información interna',
        'is_public' => false,
    ]);

    $this->get('/documentation')
        ->assertOk()
        ->assertSee('Guía pública')
        ->assertSee('Primeros pasos')
        ->assertSee(route('documents.file', $public), false)
        ->assertSee('Iniciar sesión')
        ->assertDontSee('Manual privado')
        ->assertDontSee('Información interna')
        ->assertDontSee('10.22.33.44');
});

it('searches only the public collection and renders a useful empty state', function (): void {
    Document::factory()->create(['title' => 'API pública', 'is_public' => true]);
    Document::factory()->create(['title' => 'API privada', 'is_public' => false]);

    $this->get('/documentation?q=privada')
        ->assertOk()
        ->assertSee('No hay documentos públicos que coincidan')
        ->assertDontSee('API privada');

    $this->get('/documentation?q=p%C3%BAblica')
        ->assertOk()
        ->assertSee('API pública');
});

it('escapes document metadata in the public HTML', function (): void {
    Document::factory()->create([
        'title' => '<script>alert(1)</script>',
        'is_public' => true,
    ]);

    $this->get('/documentation')
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});

it('self-canonicalizes unfiltered pagination but does not index search results', function (): void {
    Document::factory()->count(13)->create(['is_public' => true]);

    $this->get('/documentation?page=2')->assertOk()
        ->assertSee('<link rel="canonical" href="'.route('documentation.index', ['page' => 2]).'">', false)
        ->assertSee('<meta name="robots" content="index,follow">', false);

    $this->get('/documentation?q=manual&page=2')->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);
});

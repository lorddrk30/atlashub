<?php

use App\Models\Document;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the search query and count with a clear search link', function (): void {
    Document::factory()->create(['title' => 'Guía de acceso', 'is_public' => true]);

    $this->get('/documentation?q=acceso')->assertOk()
        ->assertSee('Resultados para «acceso»')
        ->assertSee('1 documento')
        ->assertSee('<a href="'.route('documentation.index').'" class="documentation-link">Limpiar búsqueda</a>', false);

    $this->get('/documentation?q=inexistente')->assertOk()
        ->assertSee('Resultados para «inexistente»')
        ->assertSee('0 documentos')
        ->assertSee('Limpiar búsqueda');

    $this->get('/documentation')->assertOk()->assertDontSee('Limpiar búsqueda');
});

it('gives PDF actions contextual accessible names and a focusable main target', function (): void {
    $document = Document::factory()->create(['title' => 'Guía de acceso', 'is_public' => true]);

    $this->get('/documentation')->assertOk()
        ->assertSee('aria-label="Abrir PDF: Guía de acceso (nueva pestaña)"', false)
        ->assertSee('aria-label="Descargar PDF: Guía de acceso"', false)
        ->assertSee(e(route('documents.file', ['document' => $document, 'download' => 1])), false)
        ->assertSee('<main id="contenido" tabindex="-1"', false);
});

it('links guests to login and authenticated readers to the intranet', function (): void {
    $this->get('/documentation')->assertOk()
        ->assertSee('aria-label="Navegación de acceso"', false)
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('Iniciar sesión')
        ->assertDontSee('Ir a la intranet');

    $this->actingAs(User::factory()->create())->get('/documentation')->assertOk()
        ->assertSee('href="'.route('portal.home').'"', false)
        ->assertSee('Ir a la intranet')
        ->assertDontSee('Iniciar sesión');
});

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

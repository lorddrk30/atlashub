<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Consulta los PDF que {{ $organization['name'] }} ha aprobado para acceso público. El inventario de sistemas y endpoints permanece en la intranet.">
    <meta name="robots" content="{{ $search !== '' ? 'noindex,follow' : 'index,follow' }}">
    <link rel="canonical" href="{{ $search === '' && $documents->currentPage() > 1 ? route('documentation.index', ['page' => $documents->currentPage()]) : route('documentation.index') }}">
    @if (! empty($organization['favicon_url']))
        <link rel="icon" href="{{ $organization['favicon_url'] }}">
    @endif
    <title>Documentación pública | {{ $organization['name'] }} · AtlasHub</title>
    @vite('resources/css/app.css')
</head>
<body class="atlas-shell documentation-page">
    <a href="#contenido" class="atlas-skip-link">Saltar al contenido</a>

    <header class="atlas-header">
        <div class="atlas-header-inner">
            <a href="{{ route('documentation.index') }}" class="atlas-brand" aria-label="{{ $organization['name'] }}: documentación pública">
                @if (! empty($organization['logo_url']))
                    <img src="{{ $organization['logo_url'] }}" alt="" class="atlas-brand-logo">
                @else
                    <span class="atlas-brand-mark" aria-hidden="true">{{ $organization['short_name'] }}</span>
                @endif
                <span class="atlas-brand-copy"><strong>{{ $organization['name'] }}</strong><span>Documentación pública</span></span>
            </a>
            <nav class="atlas-account-actions" aria-label="Navegación de acceso">
                @auth
                    <a href="{{ route('portal.home') }}" class="atlas-button atlas-button-secondary">Ir a la intranet</a>
                @else
                    <a href="{{ route('login') }}" class="atlas-button atlas-button-secondary">Iniciar sesión</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="contenido" tabindex="-1" class="atlas-content documentation-content">
        <div class="catalog-heading">
            <h1>Documentación pública</h1>
            <p class="mt-3">Consulta y descarga los PDF aprobados para acceso público. El inventario técnico permanece protegido en la intranet.</p>
        </div>

        <form action="{{ route('documentation.index') }}" method="get" role="search" class="documentation-search">
            <label for="buscar-documento" class="mb-2 block text-sm font-medium text-slate-200">Buscar documentos públicos</label>
            <div class="catalog-search">
                <svg class="atlas-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/></svg>
                <input id="buscar-documento" name="q" type="search" value="{{ $search }}" maxlength="120" placeholder="Título o descripción">
                <button type="submit" class="atlas-button atlas-button-primary">Buscar</button>
            </div>
        </form>

        <section class="documentation-results" aria-labelledby="resultados">
            <div class="documentation-results-heading">
                <div>
                    <h2 id="resultados">{{ $search !== '' ? 'Resultados de búsqueda' : 'Documentos disponibles' }}</h2>
                    @if ($search !== '')
                        <p class="documentation-query">Resultados para «{{ $search }}»</p>
                    @endif
                </div>
                <div class="documentation-result-tools">
                    <p>{{ $documents->total() }} {{ $documents->total() === 1 ? 'documento' : 'documentos' }}</p>
                    @if ($search !== '')
                        <a href="{{ route('documentation.index') }}" class="documentation-link">Limpiar búsqueda</a>
                    @endif
                </div>
            </div>

            @forelse ($documents as $document)
                <article class="documentation-row">
                    <svg class="documentation-file-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5M8 13h8M8 17h5"/></svg>
                    <div class="documentation-file-copy">
                        <h3>{{ $document->title }}</h3>
                        @if ($document->description)
                            <p class="documentation-description">{{ $document->description }}</p>
                        @endif
                        <p class="documentation-file-meta">PDF · {{ number_format($document->size / 1024, 0) }} KB</p>
                    </div>
                    <div class="documentation-file-actions">
                        <a href="{{ route('documents.file', $document) }}" target="_blank" rel="noopener noreferrer" class="atlas-button atlas-button-secondary" aria-label="Abrir PDF: {{ $document->title }} (nueva pestaña)">Abrir PDF <span aria-hidden="true">↗</span></a>
                        <a href="{{ route('documents.file', ['document' => $document, 'download' => 1]) }}" class="atlas-button atlas-button-quiet" aria-label="Descargar PDF: {{ $document->title }}">Descargar</a>
                    </div>
                </article>
            @empty
                <div class="documentation-empty">
                    @if ($search !== '')
                        <h3>No hay documentos públicos que coincidan.</h3>
                        <p>Prueba con otro título o descripción, o limpia la búsqueda para ver todos los documentos.</p>
                    @else
                        <h3>Todavía no hay documentos públicos.</h3>
                        <p>Los PDF aparecerán aquí cuando un administrador apruebe su publicación.</p>
                    @endif
                </div>
            @endforelse

            @if ($documents->hasPages())
                <nav class="documentation-pagination" aria-label="Paginación de documentos">
                    @if ($documents->onFirstPage())
                        <span>Anterior</span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" rel="prev" class="documentation-link">Anterior</a>
                    @endif
                    <span>Página {{ $documents->currentPage() }} de {{ $documents->lastPage() }}</span>
                    @if ($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" rel="next" class="documentation-link">Siguiente</a>
                    @else
                        <span>Siguiente</span>
                    @endif
                </nav>
            @endif
        </section>

        <aside class="documentation-internal" aria-labelledby="documentacion-interna">
            <div>
                <h2 id="documentacion-interna">¿Buscas documentación interna?</h2>
                <p>El inventario técnico requiere una cuenta autorizada.</p>
            </div>
            @auth
                <a href="{{ route('portal.home') }}" class="documentation-link">Ir a la intranet <span aria-hidden="true">→</span></a>
            @else
                <a href="{{ route('login') }}" class="documentation-link">Entrar a la intranet <span aria-hidden="true">→</span></a>
            @endauth
        </aside>
    </main>

    <footer class="atlas-footer"><span>Documentación pública gestionada con AtlasHub</span></footer>
</body>
</html>

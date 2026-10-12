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
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-10 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-slate-950">Saltar al contenido</a>

    <header class="border-b border-slate-800">
        <div class="mx-auto flex w-full max-w-5xl flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-8">
            <a href="{{ route('documentation.index') }}" class="flex items-center gap-3 rounded focus:outline-none focus:ring-2 focus:ring-cyan-300" aria-label="{{ $organization['name'] }}: documentación pública">
                @if (! empty($organization['logo_url']))
                    <img src="{{ $organization['logo_url'] }}" alt="" class="h-9 w-auto max-w-24 object-contain">
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded border border-slate-600 text-xs font-semibold text-slate-100" aria-hidden="true">{{ $organization['short_name'] }}</span>
                @endif
                <span class="font-semibold">{{ $organization['name'] }} <span class="font-normal text-slate-400">/ Documentación</span></span>
            </a>
            @auth
                <a href="{{ route('portal.home') }}" class="rounded-md border border-slate-600 px-4 py-2 text-sm font-medium text-slate-100 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-cyan-300">Ir a la intranet</a>
            @else
                <a href="{{ route('login') }}" class="rounded-md border border-slate-600 px-4 py-2 text-sm font-medium text-slate-100 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-cyan-300">Iniciar sesión</a>
            @endauth
        </div>
    </header>

    <main id="contenido" class="mx-auto w-full max-w-5xl px-5 py-10 sm:px-8 sm:py-14">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">Documentación pública</h1>
            <p class="mt-4 text-base leading-7 text-slate-300">Aquí encontrarás únicamente documentos PDF aprobados para acceso público. Los sistemas, direcciones IP, endpoints y reportes de la organización están protegidos en la intranet.</p>
        </div>

        <form action="{{ route('documentation.index') }}" method="get" role="search" class="mt-8 flex max-w-3xl flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label for="buscar-documento" class="mb-2 block text-sm font-medium text-slate-200">Buscar documentos públicos</label>
                <input id="buscar-documento" name="q" type="search" value="{{ $search }}" maxlength="120" placeholder="Título o descripción" class="w-full rounded-md border border-slate-600 bg-slate-900 px-4 py-2.5 text-base text-white placeholder:text-slate-400 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            </div>
            <button type="submit" class="self-start rounded-md border border-cyan-300 bg-cyan-300 px-5 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950 sm:mt-7">Buscar</button>
        </form>

        <section class="mt-10" aria-labelledby="resultados">
            <div class="flex flex-wrap items-baseline justify-between gap-3 border-b border-slate-700 pb-3">
                <h2 id="resultados" class="text-lg font-semibold text-white">Documentos disponibles</h2>
                <p class="text-sm text-slate-400">{{ $documents->total() }} {{ $documents->total() === 1 ? 'documento' : 'documentos' }}</p>
            </div>

            @forelse ($documents as $document)
                <article class="border-b border-slate-800 py-5">
                    <h3 class="text-base font-semibold text-white">{{ $document->title }}</h3>
                    @if ($document->description)
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">{{ $document->description }}</p>
                    @endif
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-400">
                        <span>PDF · {{ number_format($document->size / 1024, 0) }} KB</span>
                        <a href="{{ route('documents.file', $document) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-cyan-200 underline underline-offset-4 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-300">Abrir PDF <span class="sr-only">{{ $document->title }} (nueva pestaña)</span></a>
                        <a href="{{ route('documents.file', ['document' => $document, 'download' => 1]) }}" class="font-medium text-cyan-200 underline underline-offset-4 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-300">Descargar</a>
                    </div>
                </article>
            @empty
                <div class="py-10 text-slate-300">
                    @if ($search !== '')
                        <p class="font-medium text-white">No hay documentos públicos que coincidan.</p>
                        <p class="mt-2 text-sm">Prueba otro término o <a href="{{ route('documentation.index') }}" class="text-cyan-200 underline underline-offset-4">mira todos los documentos públicos</a>.</p>
                    @else
                        <p class="font-medium text-white">Todavía no hay documentos públicos.</p>
                        <p class="mt-2 text-sm">Un administrador puede publicar PDF aprobados desde el backoffice. El contenido privado sigue disponible solo en la intranet.</p>
                    @endif
                </div>
            @endforelse

            @if ($documents->hasPages())
                <nav class="mt-6 flex items-center justify-between gap-4 text-sm" aria-label="Paginación de documentos">
                    @if ($documents->onFirstPage())
                        <span class="text-slate-500">Anterior</span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" rel="prev" class="text-cyan-200 underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-cyan-300">Anterior</a>
                    @endif
                    <span class="text-slate-400">Página {{ $documents->currentPage() }} de {{ $documents->lastPage() }}</span>
                    @if ($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" rel="next" class="text-cyan-200 underline underline-offset-4 focus:outline-none focus:ring-2 focus:ring-cyan-300">Siguiente</a>
                    @else
                        <span class="text-slate-500">Siguiente</span>
                    @endif
                </nav>
            @endif
        </section>

        <aside class="mt-12 border-t border-slate-700 pt-6 text-sm leading-6 text-slate-300">
            <h2 class="font-semibold text-white">¿Buscas documentación interna?</h2>
            <p class="mt-1">Solicita acceso a un administrador e inicia sesión para consultar el inventario técnico de tu organización.</p>
            @guest
                <a href="{{ route('login') }}" class="mt-3 inline-block text-cyan-200 underline underline-offset-4 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-300">Entrar a la intranet</a>
            @endguest
        </aside>
    </main>

    <footer class="border-t border-slate-800 px-5 py-6 text-center text-sm text-slate-400">Documentación pública gestionada con AtlasHub</footer>
</body>
</html>

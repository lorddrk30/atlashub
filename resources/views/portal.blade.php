<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $portalConfig['portal_title'] ?? 'AtlasHub' }}</title>
        @if (! empty($portalConfig['favicon_url']))
            <link rel="icon" href="{{ $portalConfig['favicon_url'] }}">
        @endif
        <script>
            window.__ATLASHUB_CONFIG__ = @json($portalConfig);
        </script>
        @vite('resources/js/portal/main.js')
    </head>
    <body class="min-h-screen bg-[#040712] text-slate-100 antialiased">
        <div id="portal-app"></div>
    </body>
</html>

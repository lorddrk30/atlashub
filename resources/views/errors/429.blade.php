<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AtlasHub | 429</title>
        <style>
            :root {
                color-scheme: dark;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Sora', ui-sans-serif, system-ui, sans-serif;
                color: #e2e8f0;
                background: #050915;
                display: grid;
                place-items: center;
                padding: 1rem;
            }

            .panel {
                width: min(520px, 100%);
                border: 1px solid rgba(148, 163, 184, 0.28);
                border-radius: 10px;
                background: #111c33;
                box-shadow: 0 2px 8px rgba(2, 6, 23, 0.3);
                padding: 1.25rem;
            }

            .code {
                margin: 0;
                font-size: 0.875rem;
                font-weight: 600;
                color: #a5f3fc;
            }

            .title {
                margin: 0.4rem 0 0.35rem;
                font-size: 1.25rem;
                font-weight: 700;
                color: #f8fafc;
            }

            .description {
                margin: 0;
                line-height: 1.6;
                color: #cbd5e1;
                font-size: 0.95rem;
            }

            .actions {
                margin-top: 1rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn {
                appearance: none;
                border: 1px solid rgba(148, 163, 184, 0.34);
                border-radius: 8px;
                color: #e2e8f0;
                background: rgba(15, 23, 42, 0.78);
                padding: 0.6rem 0.85rem;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.85rem;
                transition: background-color .15s ease, border-color .15s ease, color .15s ease;
                cursor: pointer;
            }

            .btn:hover {
                border-color: rgba(34, 211, 238, 0.55);
                background: rgba(30, 41, 59, 0.9);
            }

            .btn-primary {
                border-color: transparent;
                color: #042028;
                background: #22d3ee;
            }

            .btn-primary:hover {
                color: #041311;
                background: #10b981;
            }
        </style>
    </head>
    <body>
        <main class="panel">
            <p class="code">Error 429</p>
            <p class="title">Demasiadas solicitudes</p>
            <p class="description">Realizaste demasiadas peticiones en poco tiempo. Espera unos segundos e intenta de nuevo.</p>

            <div class="actions">
                <a href="/" class="btn">Volver al portal</a>
                <button onclick="window.location.reload()" class="btn btn-primary">Reintentar ahora</button>
            </div>
        </main>
    </body>
</html>

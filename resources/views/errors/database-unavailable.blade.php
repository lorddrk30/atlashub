<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AtlasHub | Base de datos no disponible</title>
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
                font-family: "Segoe UI", Inter, system-ui, sans-serif;
                color: #e2e8f0;
                background:
                    radial-gradient(circle at 16% 10%, rgba(34, 211, 238, 0.22), transparent 30%),
                    radial-gradient(circle at 84% 14%, rgba(52, 211, 153, 0.2), transparent 36%),
                    linear-gradient(160deg, #040916 8%, #071025 52%, #020611 100%);
                display: grid;
                place-items: center;
                padding: 1.5rem;
            }

            .panel {
                width: min(880px, 100%);
                border-radius: 28px;
                border: 1px solid rgba(148, 163, 184, 0.26);
                background: rgba(7, 13, 29, 0.78);
                box-shadow: 0 30px 64px rgba(2, 6, 23, 0.6);
                backdrop-filter: blur(15px);
                padding: clamp(1.2rem, 2.4vw, 2rem);
                overflow: hidden;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.42rem 0.82rem;
                background: rgba(239, 68, 68, 0.2);
                border: 1px solid rgba(239, 68, 68, 0.45);
                color: #fecaca;
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 700;
            }

            h1 {
                margin: 0.85rem 0 0.55rem;
                font-size: clamp(1.8rem, 3.5vw, 2.9rem);
                line-height: 1.06;
                letter-spacing: -0.03em;
                color: #f8fafc;
                max-width: 17ch;
            }

            .lead {
                margin: 0;
                max-width: 64ch;
                line-height: 1.62;
                color: #cbd5e1;
            }

            .grid {
                margin-top: 1.2rem;
                display: grid;
                gap: 0.85rem;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .metric {
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(15, 23, 42, 0.52);
                padding: 0.78rem;
            }

            .metric .label {
                margin: 0;
                font-size: 0.71rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: #93c5fd;
            }

            .metric .value {
                margin: 0.22rem 0 0;
                color: #f8fafc;
                font-weight: 700;
                word-break: break-all;
            }

            .actions {
                margin-top: 1.2rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.7rem;
            }

            .btn {
                appearance: none;
                border: 1px solid rgba(148, 163, 184, 0.34);
                border-radius: 999px;
                color: #e2e8f0;
                background: rgba(15, 23, 42, 0.78);
                padding: 0.68rem 0.98rem;
                text-decoration: none;
                font-weight: 700;
                font-size: 0.8rem;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                transition: transform .18s ease, border-color .18s ease, filter .18s ease;
                cursor: pointer;
            }

            .btn:hover {
                transform: translateY(-1px);
                border-color: rgba(34, 211, 238, 0.56);
                filter: brightness(1.06);
            }

            .btn-primary {
                border: 0;
                color: #02231f;
                background: linear-gradient(135deg, #22d3ee, #34d399);
            }

            .hint {
                margin-top: 1rem;
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.2);
                background: rgba(2, 6, 23, 0.52);
                padding: 0.85rem;
            }

            .hint h2 {
                margin: 0;
                font-size: 0.84rem;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: #7dd3fc;
            }

            .hint pre {
                margin: 0.7rem 0 0;
                padding: 0.82rem;
                border-radius: 10px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: rgba(15, 23, 42, 0.6);
                color: #e2e8f0;
                overflow-x: auto;
                font-size: 0.78rem;
                line-height: 1.45;
            }

            @media (max-width: 740px) {
                .grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <main class="panel">
            <span class="badge">Servicio no disponible</span>
            <h1>AtlasHub no pudo conectar con la base de datos</h1>
            <p class="lead">
                El portal se mantuvo en linea, pero la conexion al motor de datos fallo. Revisa configuracion y estado del servidor para volver a operar.
            </p>

            <section class="grid" aria-label="Diagnostico de conexion">
                <article class="metric">
                    <p class="label">Conexion</p>
                    <p class="value">{{ $databaseInfo['connection'] ?? 'n/a' }}</p>
                </article>
                <article class="metric">
                    <p class="label">Host</p>
                    <p class="value">{{ $databaseInfo['host'] ?? 'n/a' }}</p>
                </article>
                <article class="metric">
                    <p class="label">Puerto</p>
                    <p class="value">{{ $databaseInfo['port'] ?? 'n/a' }}</p>
                </article>
                <article class="metric">
                    <p class="label">Base de datos</p>
                    <p class="value">{{ $databaseInfo['database'] ?? 'n/a' }}</p>
                </article>
            </section>

            <div class="actions">
                <button onclick="window.location.reload()" class="btn btn-primary">Reintentar ahora</button>
                <a href="/" class="btn">Ir al portal</a>
                <a href="/admin/login" class="btn">Ir al backoffice</a>
            </div>

            <section class="hint">
                <h2>Checklist rapido</h2>
                <pre>1) Verifica DB_DATABASE, DB_HOST, DB_PORT en .env
2) php artisan optimize:clear
3) php artisan migrate --seed</pre>
            </section>

            <p style="margin: 0.85rem 0 0; color: #94a3b8; font-size: 0.75rem;">
                Timestamp: {{ $timestamp ?? now()->format('Y-m-d H:i:s') }}
            </p>
        </main>
    </body>
</html>


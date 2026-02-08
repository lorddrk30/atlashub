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
                font-family: 'Segoe UI', Inter, system-ui, sans-serif;
                color: #e2e8f0;
                background:
                    radial-gradient(circle at 16% 10%, rgba(34, 211, 238, 0.18), transparent 28%),
                    radial-gradient(circle at 78% 20%, rgba(16, 185, 129, 0.16), transparent 34%),
                    linear-gradient(160deg, #050915 12%, #091228 55%, #040812 100%);
                display: grid;
                place-items: center;
                padding: 1.5rem;
            }

            .panel {
                width: min(640px, 100%);
                border: 1px solid rgba(148, 163, 184, 0.28);
                border-radius: 24px;
                background: rgba(8, 15, 33, 0.72);
                box-shadow: 0 24px 52px rgba(2, 6, 23, 0.56);
                backdrop-filter: blur(14px);
                padding: 2rem;
            }

            .code {
                margin: 0;
                font-size: 0.75rem;
                letter-spacing: 0.28em;
                text-transform: uppercase;
                color: rgba(165, 243, 252, 0.85);
            }

            h1 {
                margin: 0.6rem 0 0.4rem;
                font-size: clamp(1.65rem, 3.2vw, 2.4rem);
                letter-spacing: -0.02em;
                color: #f8fafc;
            }

            p {
                margin: 0;
                line-height: 1.6;
                color: #cbd5e1;
            }

            .actions {
                margin-top: 1.4rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .btn {
                appearance: none;
                border: 1px solid rgba(148, 163, 184, 0.34);
                border-radius: 999px;
                color: #e2e8f0;
                background: rgba(15, 23, 42, 0.78);
                padding: 0.68rem 0.98rem;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.83rem;
                letter-spacing: 0.06em;
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
                color: #021420;
                background: linear-gradient(135deg, #22d3ee, #34d399);
            }
        </style>
    </head>
    <body>
        <main class="panel">
            <p class="code">Error 429</p>
            <h1>🚦 Demasiadas solicitudes</h1>
            <p>Has realizado demasiadas peticiones en poco tiempo. Por favor, espera un momento antes de intentar nuevamente.</p>

            <div class="actions">
                <a href="/" class="btn">Volver al portal</a>
                <button onclick="window.location.reload()" class="btn btn-primary">Reintentar ahora ✨</button>
            </div>
        </main>
    </body>
</html>

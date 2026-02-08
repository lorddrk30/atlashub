<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AtlasHub | Sin acceso al backoffice</title>
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
                    radial-gradient(circle at 18% 12%, rgba(34, 211, 238, 0.2), transparent 30%),
                    radial-gradient(circle at 78% 22%, rgba(16, 185, 129, 0.18), transparent 32%),
                    linear-gradient(155deg, #040712 8%, #081123 56%, #050914 100%);
                display: grid;
                place-items: center;
                padding: 2rem 1.25rem;
            }

            .card {
                width: min(720px, 100%);
                border: 1px solid rgba(148, 163, 184, 0.28);
                background: rgba(8, 15, 33, 0.72);
                backdrop-filter: blur(14px);
                border-radius: 28px;
                padding: 2rem;
                box-shadow: 0 24px 50px rgba(2, 6, 23, 0.56);
            }

            .eyebrow {
                margin: 0;
                font-size: 0.72rem;
                letter-spacing: 0.22em;
                text-transform: uppercase;
                color: rgba(165, 243, 252, 0.85);
            }

            h1 {
                margin: 0.8rem 0 0.45rem;
                font-size: clamp(1.8rem, 3.8vw, 2.8rem);
                letter-spacing: -0.02em;
                color: #f8fafc;
            }

            p {
                margin: 0;
                line-height: 1.6;
                color: #cbd5e1;
            }

            .roles {
                margin-top: 1rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .role {
                border: 1px solid rgba(148, 163, 184, 0.34);
                border-radius: 999px;
                background: rgba(15, 23, 42, 0.7);
                padding: 0.32rem 0.72rem;
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #bae6fd;
            }

            .actions {
                margin-top: 1.5rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .btn {
                appearance: none;
                border: 1px solid rgba(148, 163, 184, 0.34);
                border-radius: 999px;
                color: #e2e8f0;
                background: rgba(15, 23, 42, 0.75);
                padding: 0.7rem 1rem;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.84rem;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                transition: transform .18s ease, border-color .18s ease, filter .18s ease;
                cursor: pointer;
            }

            .btn:hover {
                transform: translateY(-1px);
                border-color: rgba(34, 211, 238, 0.58);
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
        <main class="card">
            <p class="eyebrow">AtlasHub • Backoffice</p>
            <h1>🚫 Esta cuenta no tiene acceso</h1>
            <p>
                Tu perfil actual no incluye permisos de panel. Si necesitas gestionar catalogos,
                entra con una cuenta <strong>admin</strong> o <strong>editor</strong>.
            </p>

            @if ($roles->isNotEmpty())
                <div class="roles" aria-label="Roles actuales">
                    @foreach ($roles as $role)
                        <span class="role">{{ $role }}</span>
                    @endforeach
                </div>
            @endif

            <div class="actions">
                <a href="/" class="btn">Volver al portal</a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="redirect" value="/admin/login">
                    <button type="submit" class="btn btn-primary">Cambiar cuenta ✨</button>
                </form>
            </div>
        </main>
    </body>
</html>


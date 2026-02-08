# Despliegue

## Variables de entorno minimas
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY`
- `APP_URL`
- `DB_CONNECTION=pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`
- `CACHE_STORE`
- `QUEUE_CONNECTION`

## Proceso recomendado
1. `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate`
3. `php artisan config:cache`
4. `php artisan route:cache`
5. `php artisan view:cache`
6. `npm ci`
7. `npm run build`

## Verificacion post-deploy
- `php artisan test` (al menos smoke suite en pipeline)
- Probar:
  - `/`
  - `/api/v1/search`
  - `/api/v1/filters`
  - `/admin`

## Manejo de caidas de base de datos
- AtlasHub captura errores de conectividad DB y responde con:
  - Web: vista custom `resources/views/errors/database-unavailable.blade.php` (HTTP 503).
  - API: JSON consistente:
    - `message: "No fue posible conectar con la base de datos."`
    - `code: "DATABASE_UNAVAILABLE"`
    - `retryable: true`
- Objetivo: evitar pantallas tecnicas con stack trace en incidentes operativos y ofrecer una UX clara para reintento.

## Operacion
- Rotacion de logs.
- Backup de PostgreSQL.
- Monitoreo de errores HTTP 5xx y latencia de `/api/v1/search`.

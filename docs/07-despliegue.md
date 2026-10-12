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
2. `php artisan migrate --force`
3. `php artisan config:cache`
4. `php artisan route:cache`
5. `php artisan view:cache`
6. `npm ci`
7. `npm run build`
8. `php artisan atlashub:create-admin admin@example.com "Admin AtlasHub"` en terminal interactiva para crear el primer administrador (instalación nueva).

No ejecutes `db:seed` en producción: carga cuentas de demostración con contraseñas conocidas e infraestructura ficticia. En una actualización desde almacenamiento de PDF público, sigue [la migración de documentos](document-visibility.md) **antes** de volver a abrir el sitio.

**Actualización obligatoria:** mantén el sitio en mantenimiento y ejecuta `php artisan atlashub:audit-default-passwords`. Por cada cuenta detectada, ejecuta `php artisan atlashub:rotate-password correo@empresa.com` (contraseña oculta, sin argumento de línea de comandos). Repite la auditoría hasta que termine correctamente; invalida las sesiones activas si usas un almacén distinto de `database`. La contraseña de demostración está bloqueada en producción para el portal y el Backoffice, pero las cuentas existentes requieren rotación antes de quitar el mantenimiento.

## Verificacion post-deploy
- `php artisan test` (al menos smoke suite en pipeline)
- Probar:
  - `/documentation` (acceso público, solo PDF aprobados)
  - `/` (redirige a login si no hay sesión)
  - `/api/v1/search` y `/api/v1/filters` (requieren autenticación)
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

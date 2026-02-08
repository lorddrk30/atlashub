# AtlasHub

AtlasHub es un portal interno tipo Backstage-lite para catalogar y descubrir APIs internas con una unidad principal clara:

`Sistema -> Modulo -> Endpoint`

Incluye busqueda global agrupada por categorias (`sistemas`, `modulos`, `endpoints`, `artefactos`), detalle tipo mini Swagger y panel admin con Filament.

Proyecto open source: contribuciones via issues y pull requests son bienvenidas.

## Origen
AtlasHub inicio como un proyecto personal de vibecoding, creado como alternativa moderna a distintos gestores de proyectos y catalogos tecnicos tradicionales.

## Autoria
- Creador de la iniciativa abierta: **Erik Alan Alvarez** (Ingeniero de Software).
- Comunidad: cualquier persona puede contribuir para mejorar su desarrollo y evolucion.

## Stack
- Laravel 12
- PostgreSQL
- Laravel Sanctum
- Spatie Permission + Activitylog
- Filament 5
- Vue 3 + Vite + TailwindCSS
- Vue Router + Pinia + Headless UI

## Inicio rapido
1. Copiar `.env.example` a `.env`.
2. Configurar PostgreSQL en `.env`.
3. Ejecutar `composer install`.
4. Ejecutar `npm install`.
5. Ejecutar `php artisan key:generate`.
6. Ejecutar `php artisan migrate --seed`.
7. Ejecutar `php artisan serve`.
8. Ejecutar `npm run dev`.

## Credenciales demo
- `admin@rikarcoffe.local` / `password`
- `editor@rikarcoffe.local` / `password`
- `viewer@rikarcoffe.local` / `password`

## API v1 (Portal)
- `GET /api/v1/search`
- `GET /api/v1/endpoints/{public_id}`
- `GET /api/v1/filters`
- `GET /api/v1/reports/summary`
- `POST /api/v1/reports/generate-pdf`

## Admin
- URL: `/admin`
- Login backoffice: `/admin/login`
- Entrada inteligente: `/backoffice`
- Login web general (Breeze/Inertia): `/login`
- Recursos CRUD: `Systems`, `Modules`, `Endpoints`, `Artefacts`, `Users`, `Roles`, `Permissions`, `Organization Settings`
- Reportes en sidebar: `/admin/reports` (dashboard + exportables)
- Logs en sidebar: `/admin/logs` (visor visual de logs con filtros y stacktrace)
- Publicacion de endpoints controlada por permiso `endpoint.publish`
- Permisos de logs: `logs.view` (ver/descargar) y `logs.manage` (eliminar)
- Tema visual del panel: `resources/css/filament/admin/theme.css`

## Reinicio rapido de entorno
- `php artisan atlashub:reset`
- Usa `php artisan atlashub:reset --without-seed` si deseas una base limpia sin datos demo.
- Usa `php artisan atlashub:reset --yes` para ejecutarlo sin confirmacion interactiva.
- Despues, configura nombre/logo/colores en `http://127.0.0.1:8000/admin/organization-settings`.

## Documentacion
- `docs/00-fase0-plan.md`
- `docs/01-fase1-plan.md`
- `docs/02-fase2-plan.md`
- `docs/03-fase3-plan.md`
- `docs/04-fase4-plan.md`
- `docs/05-fase5-plan.md`
- `docs/06-fase6-plan.md`
- `docs/07-fase7-plan.md`
- `docs/01-instalacion.md`
- `docs/02-arquitectura.md`
- `docs/03-modelo-datos.md`
- `docs/04-api.md`
- `docs/05-admin.md`
- `docs/06-convenciones.md`
- `docs/07-despliegue.md`
- `docs/08-roadmap.md`
- `docs/09-reportes.md`
- `docs/09-contribuir.md`

## Contribuir
Guia completa: `docs/09-contribuir.md`

1. Crear branch.
2. Implementar cambio.
3. Ejecutar `php artisan test` y `npm run build`.
4. Abrir PR con descripcion tecnica y evidencia de pruebas.

## Licencia
Este proyecto se distribuye bajo licencia MIT.

Ver archivo: `LICENSE`.

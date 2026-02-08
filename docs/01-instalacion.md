# Instalacion

## Requisitos
- PHP 8.2+
- Composer 2.x
- Node.js 20+
- PostgreSQL 14+

## Pasos
1. Copiar `.env.example` a `.env`.
2. Configurar base de datos PostgreSQL.
3. Ejecutar `composer install`.
4. Ejecutar `npm install`.
5. Ejecutar `php artisan key:generate`.
6. Ejecutar `php artisan migrate --seed`.
7. Ejecutar `php artisan storage:link` (requerido para visualizar imagenes subidas desde backoffice).
8. Ejecutar `php artisan serve`.
9. Ejecutar `npm run dev`.

## Credenciales demo
- `admin@rikarcoffe.local` / `password`
- `editor@rikarcoffe.local` / `password`
- `viewer@rikarcoffe.local` / `password`

## URLs locales
- Portal: `http://127.0.0.1:8000/`
- Admin: `http://127.0.0.1:8000/admin`
- Configuracion de organizacion: `http://127.0.0.1:8000/admin/organization-settings`

## Reset para adaptar a otra organizacion
- `php artisan atlashub:reset`
- `php artisan atlashub:reset --without-seed`
- `php artisan atlashub:reset --yes`
- Despues del reset, entra a `Organization Settings` en backoffice para cambiar nombre, logo, colores y correo de soporte.

## Build de produccion local
- `npm run build`
- `php artisan test`

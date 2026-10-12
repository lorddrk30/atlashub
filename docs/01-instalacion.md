# Instalación

## Requisitos

PHP 8.2+, Composer 2, Node.js 20+ y PostgreSQL 14+. El servidor web debe apuntar a `public/` y `storage/app/private` no debe quedar expuesto por HTTP.

## Instalación nueva

1. Copia `.env.example` a `.env` y configura `APP_URL`, base de datos, correo y `ATLASHUB_ORG_*` para tu organización.
2. Instala dependencias: `composer install` y `npm install`.
3. Ejecuta `php artisan key:generate` y `php artisan migrate` (en producción, `php artisan migrate --force`).
4. Ejecuta `php artisan storage:link` para logos y otras imágenes públicas; los PDF se guardan en el disco privado.
5. En una terminal interactiva, crea el primer administrador con `php artisan atlashub:create-admin admin@example.com "Admin AtlasHub"`. El comando solicita una contraseña segura sin mostrarla ni recibirla como argumento.
6. En desarrollo, inicia `php artisan serve` y `npm run dev` en terminales separadas. En producción compila con `npm run build`.

| URL | Uso | Acceso |
| --- | --- | --- |
| `/documentation` | PDF aprobados como públicos | Sin cuenta |
| `/login` y `/` | Inicio de sesión e intranet | Cuenta asignada |
| `/backoffice` | Administración | Rol admin o editor |

No hay registro público. El administrador crea usuarios desde **Backoffice → Users**. Al cargar un PDF, la visibilidad pública está desactivada por defecto; solo el administrador puede cambiarla.

## Datos de ejemplo, solo desarrollo

`php artisan db:seed` carga la organización RikarCoffe, infraestructura ficticia y tres cuentas demo con contraseña conocida. **Nunca ejecutes este seeder en una instancia accesible a terceros.** Se bloquea en `APP_ENV=production`. Para una instalación real, usa el comando `atlashub:create-admin` en lugar de cuentas demo.

## Actualización desde almacenamiento público

Las instalaciones anteriores podían almacenar PDF bajo `storage/app/public/documents`. Sigue [la migración de documentos](document-visibility.md): mantenimiento, respaldo, `php artisan migrate --force`, `php artisan documents:migrate-to-private` y comprobación de que no queden copias públicas antes de abrir el sitio.

## Verificación

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

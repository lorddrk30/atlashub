# AtlasHub

AtlasHub es un catálogo autoadministrable de sistemas, APIs y documentación técnica. Ayuda a los equipos a encontrar responsables, entornos, endpoints, artefactos y manuales sin dispersar la información entre hojas de cálculo y wikis. Está construido con Laravel, Filament y Vue.

**Dos espacios, una regla clara:** la [documentación pública](docs/document-visibility.md) muestra solo PDF que un administrador haya aprobado; el inventario técnico y los reportes requieren iniciar sesión. Publicar un documento **no** publica el sistema o endpoint al que pertenece.

## Qué incluye

- **Intranet:** búsqueda de sistemas, módulos, endpoints, artefactos y documentos; fichas de API con métodos, rutas y ejemplos; reportes para usuarios autenticados.
- **Backoffice:** administración de inventario, usuarios, roles, permisos y marca de la organización con Filament.
- **Documentos PDF:** carga y descarga desde almacenamiento privado; visibilidad pública por documento, desactivada por defecto.
- **API v1:** consultas del catálogo protegidas con Sanctum y listado de documentos que filtra los privados para visitantes.

AtlasHub **no publica sistemas, direcciones IP ni endpoints de forma individual**. El único contenido técnico accesible sin cuenta es el PDF marcado explícitamente como público, junto con su título y descripción. Revisa cada archivo antes de publicarlo.

## Inicio rápido local

Requisitos: PHP 8.2+, Composer 2, Node.js 20+ y PostgreSQL 14+. Configura la base de datos en `.env` antes de migrar.

```bash
git clone https://github.com/lorddrk30/atlashub.git
cd atlashub
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan atlashub:create-admin admin@example.com "Admin AtlasHub"
```

El último comando pide una contraseña segura de forma interactiva y crea el primer administrador. En dos terminales, inicia la aplicación y Vite:

```bash
php artisan serve
npm run dev
```

Abre `/documentation` para ver los PDF públicos, `/login` para entrar a la intranet y `/backoffice` para administrar. Si aún no publicaste documentos, la página pública mostrará un estado vacío.

**Datos de demostración:** solo en desarrollo puedes usar `php artisan db:seed` para cargar la organización RikarCoffe, infraestructura ficticia y cuentas con contraseñas conocidas. No uses esos datos en una instancia accesible a terceros. El seeder se niega a ejecutarse en `APP_ENV=production`.

## Producción y actualizaciones

No uses `migrate --seed` ni `db:seed` en producción. Ejecuta `php artisan migrate --force`, compila con `npm run build` y crea el administrador mediante `atlashub:create-admin`. Configura HTTPS, correo, permisos de almacenamiento y las variables `ATLASHUB_ORG_*` según tu organización.

Si actualizas una instalación anterior que guardaba PDF en el disco público, **no levantes el mantenimiento todavía**: tras migrar ejecuta `php artisan documents:migrate-to-private`, verifica que no queden PDF expuestos en `storage/app/public/documents` y recién entonces revisa cuáles se harán públicos. Sigue el [procedimiento de migración y visibilidad](docs/document-visibility.md).

## Tecnologías y verificación

Laravel 12 · PHP 8.2+ · Filament 5 · Vue 3 · Inertia.js · Tailwind CSS **3** · PostgreSQL · Sanctum · Spatie Permission · Pest.

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

## Documentación

- [Instalación y configuración](docs/01-instalacion.md)
- [Arquitectura](docs/02-arquitectura.md)
- [Modelo de datos](docs/03-modelo-datos.md)
- [API v1](docs/04-api.md)
- [Administración y permisos](docs/05-admin.md)
- [Visibilidad y migración de documentos](docs/document-visibility.md)
- [Despliegue](docs/07-despliegue.md)
- [Contribuir](docs/09-contribuir.md)

## Licencia

[MIT](LICENSE). Se aceptan propuestas y mejoras mediante issues y pull requests.

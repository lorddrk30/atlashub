# Changelog

Todos los cambios relevantes de este proyecto se documentan en este archivo.

## [v1.4.0] - 2026-02-15

### Agregado
- Auto-guardado de sistemas en modo `draft` durante la creaci�n.
- Nuevo estado para sistemas: `draft`, `published`, `discarded`.
- Acci�n para `Descartar borrador` en la creaci�n de sistemas.
- Acci�n para marcar un sistema como `discarded` desde edici�n.
- Nuevos campos de IP por ambiente en sistemas:
  - `IP PROD`
  - `IP UAT`
  - `IP DEV`
- Soporte de b�squeda por IP de servidores en el cat�logo/API.
- Visualizaci�n de estado en tabla de sistemas y filtro por estado.

### Cambiado
- Al completar la creaci�n de un sistema con borrador existente, el mismo registro se publica (sin duplicar).
- El cat�logo p�blico/API ahora considera solo sistemas `published`.

### Base de datos
- Nueva migraci�n: `2026_02_15_200000_add_status_to_systems_table.php`
- Nueva migraci�n: `2026_02_15_210000_add_server_ips_to_systems_table.php`

### Actualizaci�n para instalaciones existentes
1. `git pull`
2. `composer install --no-dev --optimize-autoloader` (o `composer install` en local)
3. `npm ci && npm run build` (si aplica en tu despliegue)
4. `php artisan migrate`
5. `php artisan optimize:clear`
6. `php artisan optimize`
7. `php artisan queue:restart` (si usas workers)

### Notas
- Esta versi�n requiere ejecutar migraciones para habilitar estado e IPs en sistemas.
- Se actualizaron factory y seeder para incluir los nuevos campos.
### creador
Erik alan alvarez


# Admin

## Panel
- URL: `/admin`
- Login del panel: `/admin/login`
- Entrada recomendada desde portal: `/backoffice` (redirige segun sesion/rol)
- Tecnologia: Filament 5
- Acceso: usuarios con rol `admin` o `editor` (validado via `canAccessPanel`)

## UI Backoffice (alineada al portal)
- Tema visual personalizado en `resources/css/filament/admin/theme.css`.
- Configurado desde `app/Providers/Filament/AdminPanelProvider.php` con modo oscuro forzado, tipografia `Sora` y `renderHook(PanelsRenderHook::STYLES_AFTER, ...)` para inyectar overrides sin reemplazar el tema base de Filament.
- Login de Filament personalizado en `app/Filament/Pages/Auth/Login.php` con heading/subheading en espanol orientados a AtlasHub.
- Estilo objetivo:
  - Fondo oscuro con gradientes y glow suave.
  - Cards y paneles tipo glass.
  - Botones primarios con gradiente cian/emerald.
  - Microinteracciones sutiles en hover/focus.
- Importante: para ver cambios visuales, ejecutar `npm run dev` (desarrollo) o `npm run build` (produccion).

## Flujo UX de acceso (sin 403 seco)
- Si no hay sesion: `/backoffice` redirige a `/admin/login`.
- Si el usuario tiene rol `admin` o `editor`: `/backoffice` redirige a `/admin`.
- Si el usuario tiene rol `viewer` (u otro sin acceso): `/backoffice` redirige a `/backoffice/forbidden` con pantalla explicativa y accion `Cambiar cuenta`.
- La accion `Cambiar cuenta` cierra sesion y redirige directo a `/admin/login`.

## Modulos CRUD
- `Systems`
- `Modules`
- `Endpoints`
- `Artefacts`
- `Organization Settings`
- `Users`
- `Roles`
- `Permissions`
- `Reportes` (pagina de backoffice en `/admin/reports` con acceso al dashboard analitico `/reports`)
- `Logs` (pagina de backoffice en `/admin/logs` con visor avanzado embebido)

## UX de reportes en backoffice
- La pagina `/admin/reports` usa un layout visual propio (hero + KPIs + vista integrada) para no mostrar una pantalla tecnica plana.
- Incluye atajos a abrir el dashboard en la misma pagina o en pestana nueva.
- Muestra conteos rapidos (`systems`, `modules`, `endpoints`, `artefacts`) para contexto inmediato.

## UX de logs en backoffice
- Ruta tecnica del visor: `/admin/log-viewer` (basado en `opcodesio/log-viewer`).
- Ruta de entrada UX dentro de Filament: `/admin/logs` (hero + recomendaciones + iframe del visor).
- Acciones soportadas segun permisos:
  - Ver y descargar logs: `logs.view`
  - Eliminar archivos/carpetas de log: `logs.manage`

## Reglas de permisos (RBAC)
Permisos base sembrados en `DatabaseSeeder`:
- `system.manage`
- `module.manage`
- `endpoint.manage`
- `endpoint.publish`
- `artefact.manage`
- `organization.manage`
- `user.manage`
- `role.manage`
- `logs.view`
- `logs.manage`

Roles:
- `admin`: todos los permisos.
- `editor`: sistemas/modulos/endpoints/artefactos + publicar endpoints + lectura de logs (sin acceso a Organization Settings).
- `viewer`: sin permisos de administracion y sin acceso al panel.

## Personalizacion de marca
- Ruta: `/admin/organization-settings`
- Permiso requerido: `organization.manage`
- Campos editables:
  - Nombre de organizacion
  - Nombre corto
  - Logo/Favicon URL
  - Colores primario/secundario
  - Correo de soporte

## Publicacion de Endpoints
- Solo usuarios con `endpoint.publish` pueden cambiar `status`.
- Si un usuario sin `endpoint.publish` intenta modificar `status`, backend fuerza el valor actual (o `draft` en alta).

## Auditoria
Se mantiene `spatie/laravel-activitylog` instalado para evolucionar bitacora de cambios en siguientes iteraciones.

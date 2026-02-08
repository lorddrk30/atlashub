# Convenciones

## Backend
- Arquitectura por capas: `Domain -> Services -> Actions -> Repositories`.
- DTO para transportar filtros de busqueda.
- `FormRequest` para validaciones HTTP.
- Policies para autorizacion granular.
- Modelos con `casts` para campos JSON.

## API
- Contrato JSON estable y en espanol tecnico claro.
- Endpoints publicos en `/api/v1`.
- No exponer endpoints `draft`/`archived` en portal publico interno.

## Frontend
- UI en espanol.
- Filtros persistidos en URL.
- Navegacion por teclado (`/` para enfocar buscador).
- Modo oscuro por preferencia guardada en `localStorage`.
- Pantallas de autenticacion con estilo visual consistente al portal (dark + glass + microinteracciones), evitando apariencia de panel legacy.

## Admin
- Filament como backoffice principal.
- CRUD por entidad de catalogo (`systems`, `modules`, `endpoints`, `artefacts`).
- Publicacion de endpoint restringida por permiso `endpoint.publish`.

## Calidad
- Ejecutar `php artisan test` antes de entregar.
- Ejecutar `npm run build` para validar bundle Vue.
- Mantener docs en `/docs` sincronizadas con cada cambio relevante.

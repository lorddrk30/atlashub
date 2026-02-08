# Arquitectura

AtlasHub usa una arquitectura por capas con foco en el catalogo tecnico:

`Sistema -> Modulo -> Endpoint`

## Capas
- `App/Domain/Catalog`
  - `DTOs`: transporte de filtros de busqueda.
  - `Repositories`: consultas SQL y armado de datos.
  - `Services`: orquestacion por caso de uso.
  - `Actions`: entrada explicita para controllers.
- `App/Http`
  - Controllers API v1 (`Search`, `Endpoint`, `Filter`).
  - Form request para validacion de query params (`SearchRequest`).
- `App/Models`
  - `System`, `Module`, `Endpoint`, `Artefact`, `OrganizationSetting`.
- `App/Policies`
  - autorizacion granular por permisos.
- `App/Support`
  - `OrganizationContext` para resolver branding dinamico (portal + backoffice).
- `App/Filament`
  - backoffice CRUD para mantenimiento del catalogo.
- `resources/js/portal`
  - SPA Vue del portal interno con Pinia + Vue Router.

## Flujo de busqueda
1. `GET /api/v1/search` valida query.
2. `SearchFiltersData` normaliza filtros.
3. `SearchCatalogAction` delega a `CatalogSearchService`.
4. `CatalogSearchRepository` ejecuta consultas por categoria:
   - `systems`
   - `modules`
   - `endpoints`
   - `artefacts`
5. Se devuelve respuesta agrupada y contadores por categoria.

## Flujo de detalle endpoint
1. `GET /api/v1/endpoints/{public_id}`
2. `GetEndpointDetailAction` carga endpoint con relaciones (`module.system`, `artefacts`).
3. Solo endpoints `published` son visibles en el portal.
4. Frontend renderiza vista mini Swagger con acciones `Abrir`, `Copiar URL`, `Copiar cURL` y `Ver Swagger`.

## Consideracion de seguridad en identificadores
- El portal y la API publica no exponen IDs secuenciales para detalle de endpoints.
- Se usa `public_id` (ULID) para reducir el riesgo de enumeracion simple de recursos.

## Busqueda hoy y fase 2
- MVP: busqueda SQL (`LIKE`) con filtros y match en relaciones.
- Fase 2: swap del repositorio a Scout/Meilisearch manteniendo contrato de API.

## Nota de migracion
El proyecto conserva migraciones historicas de un catalogo legacy (`resources`/`teams`) y aplica una migracion de limpieza para eliminarlas del esquema final.

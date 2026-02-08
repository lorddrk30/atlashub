# Roadmap

## MVP actual
- Modelo de datos `Sistema -> Modulo -> Endpoint` operativo.
- Artefactos enlazados a sistema/modulo/endpoint.
- API v1 para busqueda, detalle endpoint y filtros.
- Portal Vue con resultados agrupados y vista mini Swagger.
- Admin Filament con CRUD completo de catalogo.
- RBAC con roles `admin/editor/viewer` y permisos granulares.

## Fase 2
1. Integrar Scout + Meilisearch manteniendo contrato `/api/v1/search`.
2. Ranking de relevancia con boosts por categoria (endpoint > modulo > sistema).
3. Facetas avanzadas y sugerencias/autocompletado.

## Fase 3
1. Importadores automáticos: OpenAPI/Swagger, Postman, repos Git.
2. Sincronizacion programada y deteccion de drift.
3. Workflow de aprobacion editorial para publicar cambios.

## Fase 4
1. Command palette completa (`Ctrl+K`) para navegar y ejecutar acciones.
2. Analytics de uso del portal (busquedas sin resultado, endpoints top).
3. Auditoria detallada de cambios por entidad.

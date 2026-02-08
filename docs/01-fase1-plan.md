# Plan Fase 1 - Backend Base

## Objetivo
Implementar el catalogo base `Sistema -> Modulo -> Endpoint` con API v1 y RBAC.

## Alcance
- Migraciones para `systems`, `modules`, `endpoints`, `artefacts`.
- Seeders con datos demo reales de APIs internas.
- Sanctum + Spatie Permission.
- Policies por entidad.
- Endpoints API:
  - `GET /api/v1/search`
  - `GET /api/v1/endpoints/{id}`
  - `GET /api/v1/filters`
- Tests basicos de API.

## Criterios de salida
- `php artisan migrate --seed` en verde.
- API v1 responde agrupado por categoria.
- Tests de feature en verde.

# Plan Fase 5 - Refactor Catalogo API

## Objetivo
Consolidar el pivote de AtlasHub a un catalogo de APIs real:

`Sistema -> Modulo -> Endpoint`

## Alcance
- Reemplazar flujo legacy basado en `resources` por entidades de API.
- Ajustar busqueda para indexar sistemas, modulos, endpoints y artefactos.
- Completar CRUD admin Filament de nuevas entidades.
- Endurecer RBAC con permisos granulares y control de publicacion.
- Actualizar docs, README y tests del nuevo contrato.

## Criterios de salida
- `php artisan test` en verde.
- `npm run build` en verde.
- API v1 y panel `/admin` operativos con nuevo dominio.

# Plan Fase 7 - Reportes y PDF

## Objetivo
Incorporar un modulo de reportes para AtlasHub con dashboard estadistico y generacion de PDF profesional.

## Alcance
- API v1 de reportes:
  - `GET /api/v1/reports/summary`
  - `POST /api/v1/reports/generate-pdf`
- Arquitectura por capas:
  - `DTOs` para filtros y opciones PDF
  - `ReportRepository` para consultas agregadas optimizadas
  - `ReportService` para orquestacion y construccion de documento
  - `ReportsController` como capa HTTP
- Vista Vue:
  - `ReportsDashboard.vue` con KPIs, filtros y graficas
  - Acciones de `Vista previa`, `Generar PDF` y export CSV (opcional)
- Plantilla PDF:
  - Portada, indice, resumen ejecutivo, graficas, tablas y paginado.
- Tests basicos de endpoints.

## Criterios de salida
- Dashboard funcional en `/reports`.
- PDF generado correctamente con filtro aplicado y formato profesional.
- Endpoints de reportes cubiertos con tests de feature.
- Documentacion actualizada en `docs/09-reportes.md`.


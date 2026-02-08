# Modulo de Reportes

## Objetivo
El modulo de reportes de AtlasHub agrega analitica operativa y exportables para seguimiento del catalogo tecnico:

- KPIs globales
- Graficas de distribucion
- Tablas de detalle
- Generacion de PDF profesional

## Arquitectura
Implementacion por capas:

- `app/Domain/Reports/DTOs/ReportFiltersData.php`
  - Normaliza filtros de entrada (`system`, `module`, `status`, fechas).
  - Resuelve estados de negocio (`active`, `deprecated`, `archived`) hacia estados reales de endpoint.
- `app/Domain/Reports/DTOs/ReportPdfOptionsData.php`
  - Opciones de render (`title`, `theme`, `disposition`).
- `app/Domain/Reports/Repositories/ReportRepository.php`
  - Consultas agregadas optimizadas con `group by`, `count`, `distinct`.
  - Construye datasets para KPIs, graficas y tablas.
- `app/Domain/Reports/Services/ReportService.php`
  - Orquesta resumen.
  - Construye PDF y graficas como imagenes SVG embebidas.
- `app/Http/Controllers/Api/V1/ReportsController.php`
  - Capa HTTP para endpoints de reportes.

## Endpoints
### `GET /api/v1/reports/summary`
Devuelve:
- `kpis` (totales)
- `charts` (series listas para graficas)
- `tables` (detalle)
- `filters` (catalogos para UI)
- `filters_applied`
- `executive_summary`

### `POST /api/v1/reports/generate-pdf`
Genera PDF con base en filtros actuales.

Payload relevante:
- `system_id`, `module_id`, `status`, `date_from`, `date_to`
- `title`
- `theme` (`dark|light`)
- `disposition` (`inline|download`)

## Dashboard Vue
Pantalla: `resources/js/portal/views/ReportsDashboard.vue`

Incluye:
- Cards KPI (sistemas, modulos, endpoints, artefactos)
- Panel de filtros
- Graficas (Chart.js):
  - Endpoints por sistema
  - Endpoints por modulo
  - Artefactos por tipo
  - Endpoints por metodo HTTP
- Acciones:
  - Vista previa PDF
  - Generar PDF
  - Descargar Excel (CSV)

Ruta web:
- `/reports`

## Estructura del PDF
Vista Blade: `resources/views/reports/summary-pdf.blade.php`

Secciones:
1. Portada
2. Indice
3. Resumen ejecutivo con KPIs
4. Graficas
5. Tablas detalladas

Elementos de formato:
- Encabezado y pie
- Paginado
- Tema oscuro o claro
- Logo AtlasHub

## Estado de filtros
En reportes se exponen estados de negocio:
- `active`
- `deprecated`
- `archived`

Mapeo actual hacia tabla `endpoints`:
- `active` -> `published`
- `deprecated` -> `draft` (y `deprecated` si existe en datos)
- `archived` -> `archived`

## Tests
Archivo:
- `tests/Feature/Api/V1/ReportsApiTest.php`

Cobertura minima:
- Resumen con estructura completa
- Filtros por estado
- Generacion de PDF


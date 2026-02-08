# API v1

Base URL: `/api/v1`

## GET /search
Busca en catalogo y devuelve resultados agrupados.

### Query params
- `q` (string, opcional): texto libre.
- `system_id` (int, opcional): filtrar por sistema.
- `module_id` (int, opcional): filtrar por modulo.
- `method` (string, opcional): `GET|POST|PUT|PATCH|DELETE`.
- `authentication_type` (string, opcional): `none|bearer|basic|api_key|oauth2|session|custom`.
- `artefact_type` (string, opcional): `swagger|postman|repo|docs|runbook|dashboard|other`.
- `per_category` (int, opcional, default `12`, max `50`).

### Response (200)
```json
{
  "query": "menu",
  "total": 4,
  "counts": {
    "systems": 1,
    "modules": 1,
    "endpoints": 1,
    "artefacts": 1
  },
  "grouped": {
    "systems": [],
    "modules": [],
    "endpoints": [],
    "artefacts": []
  }
}
```

Notas para `grouped.systems[*]`:
- Incluye metadatos operativos: `prod_server`, `uat_server`, `dev_server`, `internal_url`, `public_url`, `responsibles`, `user_areas`, `gitlab_url`, `home_preview_url`.
- El texto libre `q` tambien hace match contra esos campos para discovery tecnico mas rapido.

## GET /endpoints/{public_id}
Devuelve detalle de un endpoint publicado.

### Path params
- `public_id` (string ULID, requerido)

### Response (200)
```json
{
  "item": {
    "id": 10,
    "public_id": "01JMM5AN7WKS0M6AXR7H2JBN36",
    "module_id": 3,
    "name": "List menu items",
    "method": "GET",
    "path": "/api/v1/menu/items",
    "description": "Lista bebidas y productos del menu.",
    "parameters": [],
    "request_example": {},
    "response_example": {},
    "authentication_type": "bearer",
    "urls": {
      "prod": "https://api.rikarcoffe.local/api/v1/menu/items",
      "uat": "https://api-uat.rikarcoffe.local/api/v1/menu/items",
      "dev": "https://api-dev.rikarcoffe.local/api/v1/menu/items"
    },
    "status": "published",
    "module": {
      "id": 3,
      "name": "Menu Catalog",
      "system": {
        "id": 1,
        "name": "Customer Experience"
      }
    },
    "artefacts": []
  }
}
```

### Response (404)
- Cuando el endpoint no existe.
- Cuando el endpoint existe pero no esta publicado (`draft` o `archived`).

## GET /filters
Devuelve catalogos para construir filtros de la UI.

### Response (200)
```json
{
  "systems": [],
  "modules": [],
  "methods": ["GET", "POST", "PUT", "PATCH", "DELETE"],
  "authentication_types": ["none", "bearer", "basic", "api_key", "oauth2", "session", "custom"],
  "artefact_types": ["swagger", "postman", "repo", "docs", "runbook", "dashboard", "other"]
}
```

## GET /reports/summary
Devuelve KPIs, graficas agregadas y tablas para dashboard de reportes.

### Query params
- `system_id` (int, opcional)
- `module_id` (int, opcional)
- `status` (string, opcional): `active|deprecated|archived`
- `date_from` (date `Y-m-d`, opcional)
- `date_to` (date `Y-m-d`, opcional)

### Response (200)
```json
{
  "kpis": {
    "systems": 3,
    "modules": 5,
    "endpoints": 12,
    "artefacts": 9
  },
  "charts": {
    "endpoints_by_system": [],
    "endpoints_by_module": [],
    "artefacts_by_type": [],
    "endpoints_by_method": []
  },
  "tables": {
    "systems": [],
    "modules": [],
    "endpoints": [],
    "artefacts": []
  },
  "filters": {
    "systems": [],
    "modules": [],
    "statuses": []
  },
  "filters_applied": {
    "system_id": null,
    "module_id": null,
    "status": null,
    "date_from": null,
    "date_to": null
  },
  "executive_summary": "AtlasHub registra ...",
  "generated_at": "2026-02-08T20:00:00+00:00"
}
```

## POST /reports/generate-pdf
Genera reporte PDF profesional usando el resumen filtrado actual.

### Body params (JSON)
- `system_id` (int, opcional)
- `module_id` (int, opcional)
- `status` (string, opcional): `active|deprecated|archived`
- `date_from` (date `Y-m-d`, opcional)
- `date_to` (date `Y-m-d`, opcional)
- `title` (string, opcional, max `120`)
- `theme` (string, opcional): `dark|light`
- `disposition` (string, opcional): `inline|download`

### Response (200)
- `application/pdf`
- Si `disposition = inline`, el navegador puede abrir vista previa.
- Si `disposition = download`, se descarga archivo PDF.

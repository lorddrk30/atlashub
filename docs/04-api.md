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
  "total": 5,
  "counts": {
    "systems": 1,
    "modules": 1,
    "endpoints": 1,
    "artefacts": 1,
    "documents": 1
  },
  "grouped": {
    "systems": [],
    "modules": [],
    "endpoints": [],
    "artefacts": [],
    "documents": []
  }
}
```

Notas para `grouped.systems[*]`:
- Incluye metadatos operativos: `prod_server`, `uat_server`, `dev_server`, `internal_url`, `public_url`, `responsibles`, `user_areas`, `repository_url`, `home_preview_url`.
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
  "artefact_types": ["swagger", "postman", "repo", "docs", "runbook", "dashboard", "other"],
  "document_types": ["manual", "guia", "procedimiento", "diagrama", "politica"]
}
```

## Documentos

Accesos:
- `GET /documents` y `GET /documents/{id}`: lectura publica de metadata para el portal.
- `GET /documents/{id}/file`: requiere `auth:sanctum` + `document.view`.
- `POST /documents` y `DELETE /documents/{id}`: requiere `auth:sanctum` + `document.manage`.

## POST /documents
Sube un documento PDF asociado a un sistema.

### Body params (`multipart/form-data`)
- `system_id` (int, requerido)
- `module_id` (int, opcional)
- `endpoint_id` (int, opcional)
- `title` (string, requerido)
- `description` (string, opcional)
- `type` (enum: `manual|guia|procedimiento|diagrama|politica`)
- `file` (archivo PDF, max 20MB)

### Response (201)
```json
{
  "item": {
    "id": 10,
    "system_id": 1,
    "module_id": 3,
    "endpoint_id": null,
    "title": "Manual de Usuario",
    "type": "manual",
    "view_url": "http://127.0.0.1:8000/documents/10/file",
    "download_url": "http://127.0.0.1:8000/documents/10/file?download=1"
  }
}
```

## GET /documents?system_id=
Lista documentos por sistema (obligatorio), con filtros opcionales.

### Query params
- `system_id` (int, requerido)
- `module_id` (int, opcional)
- `endpoint_id` (int, opcional)
- `q` (string, opcional: busca en titulo/descripcion)

### Response (200)
```json
{
  "items": [],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 0
  }
}
```

## GET /documents/{id}
Devuelve metadata de un documento.

## GET /documents/{id}/file
Entrega el PDF autenticado.
- Inline por defecto (preview).
- `?download=1` para descarga.
- Nota UX: el portal usa las URLs `view_url/download_url` en ruta web (`/documents/{id}/file`) para evitar redirecciones no deseadas de `auth:sanctum`.

## DELETE /documents/{id}
Elimina metadata y archivo fisico del documento.

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

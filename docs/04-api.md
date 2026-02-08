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

## GET /endpoints/{id}
Devuelve detalle de un endpoint publicado.

### Path params
- `id` (int, requerido)

### Response (200)
```json
{
  "item": {
    "id": 10,
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

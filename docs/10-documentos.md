# Documentos y Manuales PDF

## Objetivo
Agregar una capa formal de manuales/documentacion PDF asociada siempre a un sistema, con opcion de asociar modulo y endpoint.

## Modelo de datos
Tabla: `documents`

Campos:
- `id`
- `system_id` (requerido)
- `module_id` (nullable)
- `endpoint_id` (nullable)
- `title`
- `description`
- `type` (`manual|guia|procedimiento|diagrama|politica`)
- `file_path`
- `mime_type`
- `size`
- `uploaded_by`
- `created_at`
- `updated_at`

Relaciones:
- `System hasMany Documents`
- `Module hasMany Documents`
- `Endpoint hasMany Documents`
- `Document belongsTo uploader (User)`

## Flujo de carga
1. Usuario con permiso `document.manage` envia `POST /api/v1/documents`.
2. Backend valida:
   - `system_id` obligatorio.
   - archivo PDF obligatorio.
   - maximo 20MB.
   - consistencia sistema/modulo/endpoint.
3. Archivo se guarda en:
   - `storage/app/public/documents/{system_slug}/`
4. Se persiste metadata en `documents`.

## Endpoints
- `POST /api/v1/documents`
- `GET /api/v1/documents?system_id=...`
- `GET /api/v1/documents/{id}`
- `GET /api/v1/documents/{id}/file` (preview inline)
- `GET /api/v1/documents/{id}/file?download=1` (descarga)
- `DELETE /api/v1/documents/{id}`
- `GET /documents/{id}/file` (ruta web para preview/descarga desde portal/backoffice)

## Permisos
- `document.view`: abrir/descargar PDF.
- `document.manage`: subir y eliminar documentos.
- Nota: el listado de metadata (`GET /documents`) es publico para permitir discovery en el portal.

## Ejemplos

### Upload (`multipart/form-data`)
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/documents" \
  -H "Authorization: Bearer <token>" \
  -F "system_id=1" \
  -F "module_id=2" \
  -F "title=Manual de usuario cafeteria" \
  -F "type=manual" \
  -F "file=@manual-cafeteria.pdf"
```

### Listado por sistema
```bash
curl "http://127.0.0.1:8000/api/v1/documents?system_id=1"
```

## Integracion Backoffice
- En `Systems` se habilita tab `Documentos`.
- El flujo de carga ocurre desde esa relacion.
- Botones rapidos:
  - `Ver PDF` (nueva pestana)
  - `Descargar`

## Integracion Portal
- Vista de sistema: seccion `Manuales y Documentacion`.
- Filtro por modulo para acotar documentos.
- Cards con tipo, descripcion, tamaño y acciones `Ver PDF` / `Descargar`.

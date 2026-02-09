# Fase 9 Plan - Manuales y PDFs por sistema

## Objetivo
Incorporar gestion de documentos PDF asociados a un sistema (obligatorio), y opcionalmente a modulo/endpoint.

## Alcance
1. Crear `documents` con relaciones a `systems`, `modules`, `endpoints` y `users`.
2. Exponer API v1 de documentos (CRUD basico + acceso seguro al archivo).
3. Agregar policy y permisos para `document.view` y `document.manage`.
4. Integrar tab de documentos en `System` dentro de Filament.
5. Crear vista de sistema en portal Vue con seccion de manuales.
6. Incluir documentos en busqueda agrupada por categoria.
7. Documentar arquitectura y uso en `docs/10-documentos.md`.

## Criterios de aceptacion
- Se pueden subir PDFs max 20MB.
- Los archivos quedan bajo `storage/app/public/documents/{system_slug}`.
- Solo usuarios autenticados con permiso pueden ver/descargar archivos.
- Portal muestra documentos filtrables por modulo.

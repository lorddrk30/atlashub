# Documentos PDF

Cada documento pertenece a un sistema y puede asociarse opcionalmente a un módulo y un endpoint. Los campos de contenido son título, descripción, tipo (`manual`, `guia`, `procedimiento`, `diagrama`, `politica`) y PDF de hasta 20 MB.

## Publicar con cuidado

1. Una cuenta con `document.manage` carga el PDF desde **Backoffice → Systems → Documentos** o por `POST /api/v1/documents`.
2. El archivo se guarda en `storage/app/private/documents/`; la visibilidad inicial es privada.
3. Solo un administrador puede activar **Visible sin iniciar sesión** después de revisar que el PDF y su título/descripción no revelan datos confidenciales.
4. El PDF público aparece en `/documentation` y `GET /api/v1/documents`. Su sistema, módulo y endpoint **permanecen en la intranet**.

Un visitante no ve documentos privados: el listado los omite y los enlaces directos devuelven 404. Para ver uno privado se requiere autenticación y permiso `document.view`. Subir y eliminar requiere `document.manage`; cambiar visibilidad requiere rol admin. No hay registro público de usuarios.

La ruta web `GET /documents/{id}/file` permite previsualizar; `?download=1` descarga. Clientes con token Sanctum usan `GET /api/v1/documents/{id}/file` con el mismo token cuando se trata de un PDF privado.

Para instalaciones existentes y reglas completas, consulta [Visibilidad y migración de documentos](document-visibility.md). Para filtros y respuestas JSON, consulta [API v1](04-api.md).

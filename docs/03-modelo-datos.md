# Modelo de Datos

## Jerarquia
- `systems`
- `modules`
- `endpoints`
- `artefacts`
- `documents`
- `organization_settings`

## systems
Campos:
- `id`
- `name` (unique)
- `slug` (unique)
- `description` (nullable)
- `prod_server` (nullable)
- `uat_server` (nullable)
- `dev_server` (nullable)
- `internal_url` (nullable)
- `public_url` (nullable)
- `responsibles` (json nullable)
- `user_areas` (json nullable)
- `repository_url` (nullable, GitHub/GitLab/Bitbucket/otro)
- `home_preview_url` (nullable)
- `timestamps`

Relaciones:
- `hasMany modules`
- `hasManyThrough endpoints`
- `hasMany artefacts`
- `hasMany documents`

## modules
Campos:
- `id`
- `system_id` (FK -> systems)
- `name`
- `slug`
- `description` (nullable)
- `status` (`active|inactive`)
- `timestamps`

Indices y constraints:
- unique (`system_id`, `slug`)
- index (`system_id`, `name`)

Relaciones:
- `belongsTo system`
- `hasMany endpoints`
- `hasMany artefacts`
- `hasMany documents`

## endpoints
Campos:
- `id`
- `public_id` (ULID unico para exposicion publica)
- `module_id` (FK -> modules)
- `name`
- `method` (`GET|POST|PUT|PATCH|DELETE`)
- `path`
- `description` (nullable, markdown compatible)
- `parameters` (json)
- `request_example` (json)
- `response_example` (json)
- `authentication_type` (`none|bearer|basic|api_key|oauth2|session|custom`)
- `urls` (json por ambiente)
- `status` (`draft|published|archived`)
- `created_by` / `updated_by` (FK -> users)
- `timestamps`

Indices y constraints:
- unique `public_id`
- unique (`module_id`, `method`, `path`)
- index (`module_id`, `status`)
- index `method`
- index `authentication_type`

Relaciones:
- `belongsTo module`
- `hasMany artefacts`
- `hasMany documents`

## documents
Campos:
- `id`
- `system_id` (FK requerido -> systems)
- `module_id` (FK nullable -> modules)
- `endpoint_id` (FK nullable -> endpoints)
- `title`
- `description` (nullable)
- `type` (`manual|guia|procedimiento|diagrama|politica`)
- `file_path` (ruta interna en `storage/app/public/documents/{system_slug}/`)
- `mime_type` (esperado `application/pdf`)
- `size` (bytes)
- `uploaded_by` (FK -> users)
- `timestamps`

Indices:
- index compuesto (`system_id`, `type`)
- index compuesto (`module_id`, `endpoint_id`)

Relaciones:
- `belongsTo system`
- `belongsTo module`
- `belongsTo endpoint`
- `belongsTo uploader (User)`

Nota de seguridad:
- El `id` autoincremental queda interno.
- Las rutas publicas del portal/API usan `public_id` para mitigar enumeracion de recursos.

## artefacts
Campos:
- `id`
- `system_id` (nullable FK -> systems)
- `module_id` (nullable FK -> modules)
- `endpoint_id` (nullable FK -> endpoints)
- `type` (`swagger|postman|repo|docs|runbook|dashboard|other`)
- `title`
- `url`
- `description` (nullable)
- `metadata` (json)
- `created_by` / `updated_by` (FK -> users)
- `timestamps`

Indices:
- index `type`
- index compuesto (`system_id`, `module_id`, `endpoint_id`)

## organization_settings
Campos:
- `id`
- `name`
- `short_name`
- `slug` (unique)
- `tagline` (nullable)
- `description` (nullable)
- `logo` (nullable, path en disco `public`)
- `favicon` (nullable, path en disco `public`)
- `support_email` (nullable)
- `primary_color`
- `secondary_color`
- `timestamps`

Uso:
- Se maneja como registro unico para personalizar branding del portal y backoffice.
- Valores por defecto tomados de `config/organization.php`.

## Seguridad (RBAC)
Tablas de `spatie/laravel-permission`:
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

Permisos de negocio:
- `system.manage`
- `module.manage`
- `endpoint.manage`
- `endpoint.publish`
- `artefact.manage`
- `organization.manage`
- `user.manage`
- `role.manage`
- `document.view`
- `document.manage`

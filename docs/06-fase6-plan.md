# Fase 6 Plan (Branding y Datos Demo)

## Objetivo
Eliminar ejemplos de gobierno y dejar el proyecto listo para una organizacion base tipo cafeteria (`RikarCoffe`), con branding editable desde backoffice.

## Alcance
- Reemplazar seeders demo por dominio de cafeteria.
- Agregar configuracion de organizacion (nombre/logo/colores/correo) en base de datos.
- Exponer configuracion en portal y marca del backoffice.
- Crear comando de reseteo local para limpiar y reconfigurar rapido.
- Actualizar documentacion tecnica y de instalacion.

## Criterios de aceptacion
- No quedan ejemplos `predial/recaudacion/tesoreria`.
- Existe tabla `organization_settings` y CRUD en Filament.
- Portal y backoffice leen el nombre/logo configurados.
- Comando `php artisan atlashub:reset` disponible y documentado.
- Build y tests pasan.


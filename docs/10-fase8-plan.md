# Fase 8 Plan - Metadatos operativos de sistemas

## Objetivo
Extender `systems` para capturar datos operativos clave y mostrarlos en portal y backoffice.

## Alcance
1. Migracion con campos de servidores, dominios, responsables, areas usuarias, GitLab y preview del home.
2. Ajuste de modelo, factory y seeders.
3. Ajuste de CRUD de Systems en Filament.
4. Incluir los nuevos campos en `GET /api/v1/search` para categoria `systems`.
5. Mejorar cards de sistemas en el portal para mostrar metadatos operativos.
6. Actualizar pruebas y documentacion.

## Criterios de aceptacion
- Se pueden crear/editar los nuevos campos desde `/admin/systems`.
- La busqueda por `q` encuentra sistemas por GitLab, servidores y dominios.
- Las cards de sistemas muestran info operativa sin romper UX.
- Pruebas de API siguen en verde.

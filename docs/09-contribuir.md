# Contribuir

AtlasHub es un proyecto open source. Cualquier persona puede proponer mejoras por medio de issues y pull requests.

## Contexto del proyecto
- AtlasHub nacio como un proyecto personal de vibecoding.
- Se plantea como alternativa abierta a varios gestores de proyectos y catalogos internos.
- Iniciativa creada por **Erik Alan Alvares**, Ingeniero de Software.

## Flujo recomendado
1. Crear un branch descriptivo (`feature/...`, `fix/...`, `docs/...`).
2. Implementar cambios pequenos y enfocados.
3. Ejecutar validaciones antes de abrir PR:
   - `php artisan test`
   - `npm run build`
4. Actualizar documentacion cuando haya cambios funcionales o de arquitectura.
5. Abrir PR explicando:
   - problema que resuelve
   - enfoque tecnico aplicado
   - evidencia de pruebas ejecutadas

## Convenciones minimas
- Mantener textos de UI en espanol.
- Respetar RBAC/policies al tocar admin.
- No romper rutas API v1 sin documentar versionado.
- Evitar cambios no relacionados dentro del mismo PR.

## Licencia
Al contribuir, aceptas que tu codigo se distribuye bajo la licencia MIT del repositorio.

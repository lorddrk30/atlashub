# AtlasHub

<div align="center">

![License](https://img.shields.io/badge/license-MIT-yellow?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5.x-F28D35?style=for-the-badge)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16+-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)

</div>

<p align="center">
  <strong>Portal interno tipo Backstage-lite para catalogar y descubrir APIs internas.</strong>
</p>

---

## 🚀 Sobre el Proyecto

**AtlasHub** es una solución moderna para la gestión del conocimiento técnico de tu organización. Permite centralizar la documentación de APIs, artefactos y sistemas en una estructura jerárquica clara:

`Sistema -> Modulo -> Endpoint`

### ✨ Características Principales

-   🔍 **Búsqueda Global Inteligente**: Encuentra sistemas, módulos, endpoints y artefactos al instante.
-   📄 **Documentación Interactiva**: Detalle de endpoints tipo mini-Swagger.
-   🛠️ **Panel de Administración**: Construido con **Filament 5** para una gestión robusta.
-   📊 **Reportes y Logs**: Visualización de actividad y métricas del sistema.
-   🔐 **Seguridad Granular**: Roles y permisos con Spatie Permission.

## 🛠️ Stack Tecnológico

Este proyecto utiliza las últimas tecnologías del ecosistema PHP y JavaScript:

-   **Backend**: Laravel 12, Laravel Sanctum, Spatie Permission/Activitylog.
-   **Admin Panel**: Filament 5.
-   **Frontend**: Vue 3, Inertia.js, TailwindCSS 4, Headless UI.
-   **Base de Datos**: PostgreSQL.
-   **Herramientas**: Vite, Pest PHP.

## ⚡ Inicio Rápido

Sigue estos pasos para levantar el entorno de desarrollo:

1.  **Clonar y configurar entorno**:
    ```bash
    cp .env.example .env
    # Configura tus credenciales de base de datos en .env
    ```

2.  **Instalar dependencias**:
    ```bash
    composer install
    npm install
    ```

3.  **Inicializar base de datos**:
    ```bash
    php artisan key:generate
    php artisan migrate --seed
    ```

4.  **Iniciar servidores**:
    ```bash
    # En terminales separadas
    php artisan serve
    npm run dev
    ```

### 🔑 Credenciales Demo

| Rol | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@rikarcoffe.local` | `password` |
| **Editor** | `editor@rikarcoffe.local` | `password` |
| **Viewer** | `viewer@rikarcoffe.local` | `password` |

## 📚 Documentación

La documentación detallada se encuentra en la carpeta `docs/`:

-   [Arquitectura](docs/02-arquitectura.md)
-   [Modelo de Datos](docs/03-modelo-datos.md)
-   [API Reference](docs/04-api.md)
-   [Guía de Administración](docs/05-admin.md)
-   [Despliegue](docs/07-despliegue.md)

## 🤝 Contribución

Este es un proyecto open source y las contribuciones son bienvenidas.

1.  Haz un Fork del repositorio.
2.  Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`).
3.  Commit de tus cambios (`git commit -m 'Add some AmazingFeature'`).
4.  Push a la rama (`git push origin feature/AmazingFeature`).
5.  Abre un Pull Request.

Consulta [docs/09-contribuir.md](docs/09-contribuir.md) para más detalles.

## 📜 Licencia

Distribuido bajo la licencia MIT. Ver `LICENSE` para más información.

---

<p align="center">
  Creado con ❤️ por <strong>Erik Alan Alvarez</strong> y la comunidad.
</p>

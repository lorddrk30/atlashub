<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>
        <style>
            @page {
                margin: 105px 42px 70px 42px;
            }

            :root {
                --bg: {{ $theme === 'light' ? '#f8fafc' : '#060f23' }};
                --panel: {{ $theme === 'light' ? '#ffffff' : '#0b1831' }};
                --text: {{ $theme === 'light' ? '#0f172a' : '#e2e8f0' }};
                --muted: {{ $theme === 'light' ? '#475569' : '#94a3b8' }};
                --border: {{ $theme === 'light' ? '#cbd5e1' : '#29405d' }};
                --accent: {{ $theme === 'light' ? '#0284c7' : '#22d3ee' }};
                --accent-soft: {{ $theme === 'light' ? '#0369a1' : '#34d399' }};
            }

            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 11px;
                color: var(--text);
                background: var(--bg);
                margin: 0;
            }

            header {
                position: fixed;
                top: -80px;
                left: 0;
                right: 0;
                height: 60px;
                border-bottom: 1px solid var(--border);
                color: var(--muted);
                font-size: 10px;
            }

            footer {
                position: fixed;
                bottom: -48px;
                left: 0;
                right: 0;
                height: 30px;
                border-top: 1px solid var(--border);
                color: var(--muted);
                font-size: 10px;
            }

            .header-left,
            .footer-left {
                float: left;
            }

            .header-right,
            .footer-right {
                float: right;
                text-align: right;
            }

            .pagenum::before {
                content: counter(page);
            }

            .section {
                margin-bottom: 22px;
            }

            .cover {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: 16px;
                padding: 26px;
                min-height: 420px;
                page-break-after: always;
            }

            .logo {
                width: 78px;
                height: 78px;
            }

            .kicker {
                color: var(--accent);
                text-transform: uppercase;
                letter-spacing: 0.18em;
                font-size: 10px;
                margin-top: 20px;
            }

            h1 {
                font-size: 34px;
                line-height: 1.08;
                margin: 10px 0 8px;
            }

            h2 {
                font-size: 20px;
                margin: 0 0 14px;
            }

            h3 {
                font-size: 14px;
                margin: 0 0 10px;
            }

            .muted {
                color: var(--muted);
            }

            .index {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 22px;
                page-break-after: always;
            }

            .index-row {
                border-bottom: 1px dashed var(--border);
                padding: 8px 0;
            }

            .index-label {
                float: left;
            }

            .index-page {
                float: right;
                color: var(--muted);
            }

            .panel {
                background: var(--panel);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 16px;
            }

            .kpi-grid {
                margin-top: 14px;
            }

            .kpi-card {
                width: 48%;
                display: inline-block;
                margin-bottom: 10px;
                margin-right: 2%;
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px;
                background: var(--bg);
                vertical-align: top;
            }

            .kpi-label {
                margin: 0;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--muted);
            }

            .kpi-value {
                margin: 6px 0 0;
                font-size: 24px;
                color: var(--accent);
                font-weight: bold;
            }

            .chart {
                margin-bottom: 18px;
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px;
                background: var(--bg);
            }

            .chart img {
                width: 100%;
                max-height: 260px;
                object-fit: contain;
            }

            .table-title {
                margin: 0 0 8px;
                color: var(--accent-soft);
                font-size: 13px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 14px;
                font-size: 10px;
            }

            th, td {
                border: 1px solid var(--border);
                padding: 6px 7px;
                text-align: left;
                vertical-align: top;
            }

            th {
                background: var(--bg);
                color: var(--accent);
                text-transform: uppercase;
                font-size: 9px;
                letter-spacing: 0.06em;
            }

            .page-break {
                page-break-after: always;
            }

            .legend {
                margin-top: 6px;
                color: var(--muted);
                font-size: 10px;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="header-left">
                <strong>AtlasHub</strong><br>
                Reporte generado: {{ $generatedAt }}
            </div>
            <div class="header-right">
                {{ $title }}<br>
                Tema: {{ strtoupper($theme) }}
            </div>
        </header>

        <footer>
            <div class="footer-left">AtlasHub Reports</div>
            <div class="footer-right">Pagina <span class="pagenum"></span></div>
        </footer>

        <main>
            <section class="cover">
                <img src="{{ $logoDataUri }}" alt="AtlasHub" class="logo">
                <p class="kicker">Reporte AtlasHub</p>
                <h1>{{ $title }}</h1>
                <p class="muted">Generado el {{ $generatedAt }}</p>
                <p style="margin-top: 24px; line-height: 1.7;">
                    Este documento resume el estado del catalogo tecnico de AtlasHub: sistemas, modulos, endpoints y artefactos.
                    Incluye indicadores clave, visualizaciones de distribucion y tablas de referencia para analisis operativo.
                </p>
                <div style="margin-top: 24px; border-top: 1px solid var(--border); padding-top: 16px;">
                    <strong>Filtros aplicados</strong>
                    <p class="muted" style="line-height: 1.7;">
                        Sistema: {{ $summary['filters_applied']['system_id'] ?? 'Todos' }} |
                        Modulo: {{ $summary['filters_applied']['module_id'] ?? 'Todos' }} |
                        Estado: {{ $summary['filters_applied']['status'] ?? 'Todos' }} |
                        Fecha desde: {{ $summary['filters_applied']['date_from'] ?? 'N/A' }} |
                        Fecha hasta: {{ $summary['filters_applied']['date_to'] ?? 'N/A' }}
                    </p>
                </div>
            </section>

            <section class="index">
                <h2>Indice</h2>
                <div class="index-row">
                    <span class="index-label">1. Resumen ejecutivo</span>
                    <span class="index-page">3</span>
                    <div style="clear: both;"></div>
                </div>
                <div class="index-row">
                    <span class="index-label">2. Graficas de distribucion</span>
                    <span class="index-page">4</span>
                    <div style="clear: both;"></div>
                </div>
                <div class="index-row">
                    <span class="index-label">3. Tablas detalladas</span>
                    <span class="index-page">5</span>
                    <div style="clear: both;"></div>
                </div>
            </section>

            <section class="section panel page-break">
                <h2>Resumen ejecutivo</h2>
                <p class="muted" style="line-height: 1.7;">{{ $summary['executive_summary'] }}</p>

                <div class="kpi-grid">
                    <article class="kpi-card">
                        <p class="kpi-label">Total sistemas</p>
                        <p class="kpi-value">{{ $summary['kpis']['systems'] }}</p>
                    </article>
                    <article class="kpi-card">
                        <p class="kpi-label">Total modulos</p>
                        <p class="kpi-value">{{ $summary['kpis']['modules'] }}</p>
                    </article>
                    <article class="kpi-card">
                        <p class="kpi-label">Total endpoints</p>
                        <p class="kpi-value">{{ $summary['kpis']['endpoints'] }}</p>
                    </article>
                    <article class="kpi-card">
                        <p class="kpi-label">Total artefactos</p>
                        <p class="kpi-value">{{ $summary['kpis']['artefacts'] }}</p>
                    </article>
                </div>
            </section>

            <section class="section panel page-break">
                <h2>Graficas de distribucion</h2>
                <div class="chart">
                    <img src="{{ $chartImages['endpoints_by_system'] }}" alt="Endpoints por sistema">
                    <p class="legend">Distribucion de endpoints agrupados por sistema.</p>
                </div>
                <div class="chart">
                    <img src="{{ $chartImages['endpoints_by_module'] }}" alt="Endpoints por modulo">
                    <p class="legend">Distribucion de endpoints por modulo.</p>
                </div>
                <div class="chart">
                    <img src="{{ $chartImages['artefacts_by_type'] }}" alt="Artefactos por tipo">
                    <p class="legend">Participacion de artefactos segun su tipo.</p>
                </div>
                <div class="chart">
                    <img src="{{ $chartImages['endpoints_by_method'] }}" alt="Endpoints por metodo HTTP">
                    <p class="legend">Frecuencia de endpoints por metodo HTTP.</p>
                </div>
            </section>

            <section class="section panel">
                <h2>Tablas detalladas</h2>

                <h3 class="table-title">Sistemas</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Sistema</th>
                            <th>Endpoints</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['tables']['systems'] as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['endpoint_count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Sin datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h3 class="table-title">Modulos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Modulo</th>
                            <th>Sistema</th>
                            <th>Endpoints</th>
                            <th>Ultima actualizacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['tables']['modules'] as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['system_name'] }}</td>
                                <td>{{ $row['endpoint_count'] }}</td>
                                <td>{{ $row['last_update'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Sin datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h3 class="table-title">Endpoints (muestra)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Metodo</th>
                            <th>Path</th>
                            <th>Estado</th>
                            <th>Modulo</th>
                            <th>Sistema</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['tables']['endpoints'] as $row)
                            <tr>
                                <td>{{ $row['method'] }}</td>
                                <td>{{ $row['path'] }}</td>
                                <td>{{ $row['status'] }}</td>
                                <td>{{ $row['module_name'] }}</td>
                                <td>{{ $row['system_name'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Sin datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h3 class="table-title">Artefactos (muestra)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Titulo</th>
                            <th>Modulo</th>
                            <th>Sistema</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['tables']['artefacts'] as $row)
                            <tr>
                                <td>{{ $row['type'] }}</td>
                                <td>{{ $row['title'] }}</td>
                                <td>{{ $row['module_name'] ?? '-' }}</td>
                                <td>{{ $row['system_name'] ?? '-' }}</td>
                                <td>{{ $row['url'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Sin datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </main>
    </body>
</html>


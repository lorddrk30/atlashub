<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>
        <style>
            @page {
                margin: 90px 36px 58px 36px;
            }

            :root {
                --paper: {{ $theme === 'light' ? '#ffffff' : '#0b1324' }};
                --canvas: {{ $theme === 'light' ? '#f1f5f9' : '#061024' }};
                --text: {{ $theme === 'light' ? '#0f172a' : '#e2e8f0' }};
                --muted: {{ $theme === 'light' ? '#475569' : '#94a3b8' }};
                --line: {{ $theme === 'light' ? '#cbd5e1' : '#29405d' }};
                --accent: {{ $theme === 'light' ? '#0369a1' : '#22d3ee' }};
                --accent-soft: {{ $theme === 'light' ? '#0f766e' : '#34d399' }};
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 10.6px;
                color: var(--text);
                background: var(--canvas);
                line-height: 1.45;
            }

            header {
                position: fixed;
                top: -72px;
                left: 0;
                right: 0;
                height: 56px;
                border-bottom: 1px solid var(--line);
                padding-bottom: 8px;
            }

            footer {
                position: fixed;
                bottom: -42px;
                left: 0;
                right: 0;
                height: 24px;
                border-top: 1px solid var(--line);
                color: var(--muted);
                font-size: 9px;
                padding-top: 6px;
            }

            .left {
                float: left;
            }

            .right {
                float: right;
                text-align: right;
            }

            .pagenum:before {
                content: counter(page);
            }

            .cover {
                background: var(--paper);
                border: 1px solid var(--line);
                border-radius: 14px;
                padding: 22px;
                margin-bottom: 14px;
            }

            .brand {
                margin: 0;
                color: var(--accent);
                text-transform: uppercase;
                letter-spacing: 0.18em;
                font-size: 9px;
                font-weight: 700;
            }

            h1 {
                margin: 8px 0 8px;
                font-size: 28px;
                line-height: 1.12;
            }

            h2 {
                margin: 0 0 12px;
                color: var(--accent);
                font-size: 16px;
            }

            h3 {
                margin: 0 0 8px;
                font-size: 12px;
                color: var(--accent-soft);
            }

            .muted {
                color: var(--muted);
            }

            .section {
                margin-bottom: 12px;
                background: var(--paper);
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 14px;
            }

            .kpi-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 8px;
                margin: 6px -8px 0;
            }

            .kpi-table td {
                width: 25%;
                border: 1px solid var(--line);
                background: var(--canvas);
                border-radius: 10px;
                padding: 9px;
                vertical-align: top;
            }

            .kpi-label {
                margin: 0;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: 0.07em;
                font-size: 8.5px;
            }

            .kpi-value {
                margin: 5px 0 0;
                color: var(--accent);
                font-size: 22px;
                font-weight: 700;
            }

            .chart-box {
                border: 1px solid var(--line);
                border-radius: 10px;
                background: var(--canvas);
                padding: 8px;
                margin-bottom: 10px;
            }

            .chart-box img {
                width: 100%;
                max-height: 220px;
                object-fit: contain;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 12px;
                font-size: 9.5px;
            }

            th,
            td {
                border: 1px solid var(--line);
                padding: 5px 6px;
                text-align: left;
                vertical-align: top;
            }

            th {
                background: var(--canvas);
                color: var(--accent);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-size: 8px;
            }

            .tag {
                display: inline-block;
                padding: 2px 7px;
                border-radius: 999px;
                border: 1px solid var(--line);
                font-size: 8.5px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .tag-draft {
                color: #b45309;
                border-color: #f59e0b;
                background: #fffbeb;
            }

            .tag-published {
                color: #166534;
                border-color: #22c55e;
                background: #f0fdf4;
            }

            .tag-discarded {
                color: #991b1b;
                border-color: #ef4444;
                background: #fef2f2;
            }

            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="left">
                <strong>AtlasHub Reportes</strong><br>
                <span class="muted">Generado: {{ $generatedAt }}</span>
            </div>
            <div class="right">
                {{ $title }}<br>
                <span class="muted">Tema {{ strtoupper($theme) }}</span>
            </div>
        </header>

        <footer>
            <div class="left">AtlasHub</div>
            <div class="right">Pagina <span class="pagenum"></span></div>
        </footer>

        <main>
            <section class="cover">
                <p class="brand">Reporte ejecutivo</p>
                <h1>{{ $title }}</h1>
                <p class="muted">
                    Este reporte consolida sistemas, modulos, endpoints y artefactos. Incluye sistemas en estado borrador, publicado y descartado.
                </p>
                <p class="muted" style="margin-top: 12px;">
                    Filtros: sistema={{ $summary['filters_applied']['system_id'] ?? 'todos' }} |
                    modulo={{ $summary['filters_applied']['module_id'] ?? 'todos' }} |
                    estado sistema={{ $summary['filters_applied']['system_status'] ?? 'todos' }} |
                    estado endpoint={{ $summary['filters_applied']['status'] ?? 'todos' }} |
                    desde={{ $summary['filters_applied']['date_from'] ?? 'N/A' }} |
                    hasta={{ $summary['filters_applied']['date_to'] ?? 'N/A' }}
                </p>
            </section>

            <section class="section page-break">
                <h2>Resumen ejecutivo</h2>
                <p class="muted" style="margin-top: 0;">{{ $summary['executive_summary'] }}</p>

                <table class="kpi-table">
                    <tr>
                        <td>
                            <p class="kpi-label">Sistemas</p>
                            <p class="kpi-value">{{ $summary['kpis']['systems'] }}</p>
                        </td>
                        <td>
                            <p class="kpi-label">Modulos</p>
                            <p class="kpi-value">{{ $summary['kpis']['modules'] }}</p>
                        </td>
                        <td>
                            <p class="kpi-label">Endpoints</p>
                            <p class="kpi-value">{{ $summary['kpis']['endpoints'] }}</p>
                        </td>
                        <td>
                            <p class="kpi-label">Artefactos</p>
                            <p class="kpi-value">{{ $summary['kpis']['artefacts'] }}</p>
                        </td>
                    </tr>
                </table>

                <h3>Distribucion por estado de sistema</h3>
                <div class="chart-box">
                    <img src="{{ $chartImages['systems_by_status'] }}" alt="Sistemas por estado">
                </div>
            </section>

            <section class="section page-break">
                <h2>Graficas operativas</h2>
                <div class="chart-box">
                    <img src="{{ $chartImages['endpoints_by_system'] }}" alt="Endpoints por sistema">
                </div>
                <div class="chart-box">
                    <img src="{{ $chartImages['endpoints_by_module'] }}" alt="Endpoints por modulo">
                </div>
                <div class="chart-box">
                    <img src="{{ $chartImages['artefacts_by_type'] }}" alt="Artefactos por tipo">
                </div>
                <div class="chart-box">
                    <img src="{{ $chartImages['endpoints_by_method'] }}" alt="Endpoints por metodo HTTP">
                </div>
            </section>

            <section class="section">
                <h2>Tablas detalladas</h2>

                <h3>Sistemas (incluye borradores)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Sistema</th>
                            <th>Slug</th>
                            <th>Estado</th>
                            <th>Endpoints</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['tables']['systems'] as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['slug'] }}</td>
                                <td>
                                    @php($status = $row['status'])
                                    <span class="tag tag-{{ $status }}">{{ $status }}</span>
                                </td>
                                <td>{{ $row['endpoint_count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Sin datos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <h3>Modulos</h3>
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

                <h3>Endpoints (muestra)</h3>
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

                <h3>Artefactos (muestra)</h3>
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

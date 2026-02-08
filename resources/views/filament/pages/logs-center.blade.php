<x-filament-panels::page>
    <section style="display:grid;gap:1rem;">
        <article style="border:1px solid rgba(125,211,252,.24);border-radius:20px;padding:1.2rem;background:linear-gradient(135deg,rgba(7,22,48,.96),rgba(4,15,35,.92));box-shadow:0 20px 45px rgba(2,8,23,.5);">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem;align-items:start;">
                <div>
                    <p style="margin:0;color:#7dd3fc;text-transform:uppercase;letter-spacing:.18em;font-size:.72rem;font-weight:700;">Seguridad operativa</p>
                    <h2 style="margin:.45rem 0 0;color:#f8fafc;font-size:clamp(1.45rem,2.6vw,2rem);line-height:1.12;font-weight:700;">Centro visual de logs</h2>
                    <p style="margin:.7rem 0 0;color:#cbd5e1;line-height:1.55;max-width:62ch;">
                        Monitorea errores, advertencias y eventos inusuales desde una interfaz avanzada.
                        Desde aqui puedes filtrar por nivel, archivo, fecha y stacktrace para diagnosticar mas rapido.
                    </p>
                    <div style="margin-top:.95rem;display:flex;flex-wrap:wrap;gap:.55rem;">
                        <a href="{{ $this->getLogViewerUrl() }}" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.6rem 1rem;font-size:.78rem;font-weight:700;letter-spacing:.04em;color:#042126;background:linear-gradient(135deg,#22d3ee,#34d399);">
                            Abrir logs completo
                        </a>
                        <a href="{{ $this->getLogViewerUrl() }}" target="_blank" rel="noopener noreferrer" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:.6rem 1rem;font-size:.78rem;font-weight:700;letter-spacing:.04em;color:#e2e8f0;background:rgba(15,23,42,.72);border:1px solid rgba(148,163,184,.35);">
                            Abrir en nueva pestana
                        </a>
                    </div>
                </div>

                <div style="border:1px solid rgba(125,211,252,.2);border-radius:16px;background:rgba(2,12,30,.66);padding:.9rem;display:grid;gap:.65rem;">
                    <p style="margin:0;color:#a5f3fc;font-size:.7rem;text-transform:uppercase;letter-spacing:.18em;font-weight:700;">Buenas practicas</p>
                    <ul style="margin:0;padding-left:1rem;color:#cbd5e1;display:grid;gap:.45rem;font-size:.9rem;line-height:1.45;">
                        <li>Valida picos de errores 5xx despues de cada despliegue.</li>
                        <li>Busca trazas repetidas para detectar regresiones.</li>
                        <li>Restringe borrado de logs a roles administrativos.</li>
                    </ul>
                </div>
            </div>
        </article>

        <article style="border:1px solid rgba(125,211,252,.2);border-radius:20px;background:rgba(3,12,30,.76);box-shadow:inset 0 0 0 1px rgba(148,163,184,.1);padding:.6rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem;padding:.45rem .4rem .65rem;">
                <p style="margin:0;color:#e2e8f0;font-weight:600;font-size:.9rem;">Vista integrada de Log Viewer</p>
                <span style="color:#94a3b8;font-size:.78rem;">Permiso requerido: <code style="font-size:.75rem;">logs.view</code></span>
            </div>

            <iframe
                src="{{ $this->getLogViewerUrl() }}"
                title="Vista integrada de logs AtlasHub"
                style="width:100%;height:72vh;min-height:680px;max-height:980px;border:0;border-radius:14px;background:#030712;"
            ></iframe>
        </article>
    </section>
</x-filament-panels::page>

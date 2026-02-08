<x-filament-panels::page>
    <section
        style="
            display: grid;
            gap: 1rem;
        "
    >
        <article
            style="
                border: 1px solid rgba(125, 211, 252, 0.24);
                border-radius: 20px;
                padding: 1.2rem;
                background: linear-gradient(135deg, rgba(7, 22, 48, 0.96), rgba(4, 15, 35, 0.92));
                box-shadow: 0 20px 45px rgba(2, 8, 23, 0.5);
            "
        >
            <div
                style="
                    display: grid;
                    gap: 1rem;
                    align-items: start;
                    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                "
            >
                <div>
                    <p
                        style="
                            margin: 0;
                            color: #7dd3fc;
                            text-transform: uppercase;
                            letter-spacing: 0.18em;
                            font-size: 0.72rem;
                            font-weight: 700;
                        "
                    >
                        Modulo de reportes
                    </p>
                    <h2
                        style="
                            margin: 0.45rem 0 0;
                            color: #f8fafc;
                            font-size: clamp(1.45rem, 2.6vw, 2rem);
                            line-height: 1.12;
                            font-weight: 700;
                        "
                    >
                        Analitica ejecutiva sin salir del backoffice
                    </h2>
                    <p
                        style="
                            margin: 0.7rem 0 0;
                            color: #cbd5e1;
                            line-height: 1.55;
                            max-width: 62ch;
                        "
                    >
                        Consulta indicadores del catalogo tecnico, aplica filtros y genera PDF o CSV.
                        La experiencia es la misma del portal, integrada y sin friccion.
                    </p>
                    <div
                        style="
                            margin-top: 0.95rem;
                            display: flex;
                            flex-wrap: wrap;
                            gap: 0.55rem;
                        "
                    >
                        <a
                            href="{{ route('portal.reports') }}"
                            style="
                                text-decoration: none;
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                border-radius: 999px;
                                padding: 0.6rem 1rem;
                                font-size: 0.78rem;
                                font-weight: 700;
                                letter-spacing: 0.04em;
                                color: #042126;
                                background: linear-gradient(135deg, #22d3ee, #34d399);
                            "
                        >
                            Abrir dashboard completo
                        </a>
                        <a
                            href="{{ route('portal.reports') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="
                                text-decoration: none;
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                border-radius: 999px;
                                padding: 0.6rem 1rem;
                                font-size: 0.78rem;
                                font-weight: 700;
                                letter-spacing: 0.04em;
                                color: #e2e8f0;
                                background: rgba(15, 23, 42, 0.72);
                                border: 1px solid rgba(148, 163, 184, 0.35);
                            "
                        >
                            Abrir en nueva pestana
                        </a>
                    </div>
                </div>

                <div
                    style="
                        border: 1px solid rgba(125, 211, 252, 0.2);
                        border-radius: 16px;
                        background: rgba(2, 12, 30, 0.66);
                        padding: 0.9rem;
                        display: grid;
                        gap: 0.65rem;
                    "
                >
                    <p
                        style="
                            margin: 0;
                            color: #a5f3fc;
                            font-size: 0.7rem;
                            text-transform: uppercase;
                            letter-spacing: 0.18em;
                            font-weight: 700;
                        "
                    >
                        Snapshot rapido
                    </p>
                    <div
                        style="
                            display: grid;
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                            gap: 0.55rem;
                        "
                    >
                        @foreach ($this->stats as $stat)
                            <div
                                style="
                                    border: 1px solid rgba(148, 163, 184, 0.2);
                                    border-radius: 12px;
                                    background: rgba(15, 23, 42, 0.64);
                                    padding: 0.6rem 0.7rem;
                                "
                            >
                                <p
                                    style="
                                        margin: 0;
                                        font-size: 0.67rem;
                                        color: #93c5fd;
                                        text-transform: uppercase;
                                        letter-spacing: 0.14em;
                                        font-weight: 700;
                                    "
                                >
                                    {{ $stat['label'] }}
                                </p>
                                <p
                                    style="
                                        margin: 0.2rem 0 0;
                                        color: #f8fafc;
                                        font-size: 1.2rem;
                                        font-weight: 700;
                                    "
                                >
                                    {{ $stat['value'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </article>

        <article
            style="
                border: 1px solid rgba(125, 211, 252, 0.2);
                border-radius: 20px;
                background: rgba(3, 12, 30, 0.76);
                box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.1);
                padding: 0.6rem;
            "
        >
            <div
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 0.4rem;
                    padding: 0.45rem 0.4rem 0.65rem;
                "
            >
                <p
                    style="
                        margin: 0;
                        color: #e2e8f0;
                        font-weight: 600;
                        font-size: 0.9rem;
                    "
                >
                    Vista integrada del dashboard de reportes
                </p>
                <span
                    style="
                        color: #94a3b8;
                        font-size: 0.78rem;
                    "
                >
                    Usa los filtros dentro del tablero y exporta desde ahi
                </span>
            </div>

            <iframe
                src="{{ route('portal.reports') }}"
                title="Vista integrada de reportes AtlasHub"
                style="
                    width: 100%;
                    height: 72vh;
                    min-height: 680px;
                    max-height: 980px;
                    border: 0;
                    border-radius: 14px;
                    background: #030712;
                "
            ></iframe>
        </article>
    </section>
</x-filament-panels::page>

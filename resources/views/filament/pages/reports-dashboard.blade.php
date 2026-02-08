<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">
        <section class="rounded-2xl border border-white/10 bg-slate-950/60 p-5 md:col-span-2">
            <p class="text-xs uppercase tracking-[0.2em] text-cyan-200/80">Modulo de reportes</p>
            <h2 class="mt-2 text-2xl font-semibold text-white">Dashboard de estadisticas y exportables</h2>
            <p class="mt-2 text-sm text-slate-300">
                Consulta KPIs del catalogo tecnico, aplica filtros y genera reportes en PDF desde un flujo visual unificado.
            </p>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('portal.reports') }}"
                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-2 text-xs font-semibold text-slate-900 transition hover:-translate-y-0.5"
                >
                    Abrir dashboard de reportes
                </a>

                <a
                    href="{{ route('portal.reports') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center rounded-xl border border-white/20 bg-white/[0.05] px-4 py-2 text-xs font-semibold text-white transition hover:border-cyan-300/60"
                >
                    Abrir en nueva pestaña
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-white/10 bg-slate-950/60 p-5">
            <h3 class="text-sm font-semibold uppercase tracking-[0.14em] text-cyan-200/80">Atajos</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-300">
                <li>1. Filtrar por sistema y modulo.</li>
                <li>2. Revisar distribuciones en graficas.</li>
                <li>3. Generar PDF ejecutivo o CSV.</li>
            </ul>
        </section>
    </div>

    <section class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/60 p-2">
        <iframe
            src="{{ route('portal.reports') }}"
            title="Vista previa de reportes AtlasHub"
            class="h-[820px] w-full rounded-xl border border-white/10 bg-slate-950/50"
        ></iframe>
    </section>
</x-filament-panels::page>


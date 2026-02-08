<template>
  <section class="space-y-7 pb-10 md:space-y-10 md:pb-14">
    <div class="relative overflow-hidden rounded-[32px] border border-white/15 bg-white/[0.04] p-6 shadow-2xl shadow-cyan-950/35 backdrop-blur-xl md:p-9">
      <div class="atlas-grid pointer-events-none absolute inset-0 rounded-[32px] opacity-35" />
      <div class="relative">
        <p class="text-[11px] uppercase tracking-[0.28em] text-cyan-200/80">Analytics + Reportes</p>
        <h1 class="display-title mt-2 text-4xl font-semibold leading-tight text-white md:text-5xl">Panel de reportes AtlasHub</h1>
        <p class="mt-3 max-w-3xl text-sm text-slate-300 md:text-base">
          Consulta indicadores del catalogo tecnico, aplica filtros y genera reportes PDF con formato ejecutivo.
        </p>
      </div>
    </div>

    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-4 backdrop-blur-xl md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Control de reporte</p>
          <h2 class="display-title text-xl text-white">Filtros</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <button class="rounded-full border border-white/15 px-3 py-1.5 text-xs text-slate-200 transition hover:border-cyan-300/50" @click="resetFilters">Limpiar</button>
          <button class="rounded-full bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-1.5 text-xs font-semibold text-slate-900 transition hover:-translate-y-0.5" @click="loadSummary">Aplicar</button>
        </div>
      </div>

      <div class="mt-4 grid gap-3 md:grid-cols-3">
        <select v-model="filters.system_id" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Sistema</option>
          <option v-for="item in filterCatalog.systems" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="filters.module_id" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Modulo</option>
          <option v-for="item in moduleOptions" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="filters.status" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Estado</option>
          <option v-for="item in filterCatalog.statuses" :key="item.value" :value="item.value">{{ item.label }}</option>
        </select>
        <input v-model="filters.date_from" type="date" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
        <input v-model="filters.date_to" type="date" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
        <select v-model="filters.theme" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="dark">Tema PDF: oscuro</option>
          <option value="light">Tema PDF: claro</option>
        </select>
      </div>
    </section>

    <section class="rounded-3xl border border-white/10 bg-white/[0.03] p-4 md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <h2 class="display-title text-xl text-white">Generacion de reportes</h2>
        <div class="flex flex-wrap items-center gap-2">
          <button
            class="rounded-xl border border-white/20 bg-white/[0.05] px-4 py-2 text-xs text-white transition hover:border-cyan-300/60 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="generatingPdf"
            @click="generatePdf('inline')"
          >
            Vista previa
          </button>
          <button
            class="rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-2 text-xs font-semibold text-slate-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="generatingPdf"
            @click="generatePdf('download')"
          >
            Generar PDF
          </button>
          <button
            class="rounded-xl border border-white/20 bg-white/[0.05] px-4 py-2 text-xs text-white transition hover:border-cyan-300/60 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="loadingSummary || !summary"
            @click="downloadCsv"
          >
            Descargar Excel (CSV)
          </button>
        </div>
      </div>
      <p v-if="generatingPdf" class="mt-2 text-xs text-cyan-100">Generando reporte PDF, por favor espera...</p>
    </section>

    <div v-if="loadingSummary" class="rounded-3xl border border-white/10 bg-white/[0.03] p-6 text-sm text-slate-300 animate-pulse">
      Cargando estadisticas del catalogo...
    </div>
    <div v-else-if="errorMessage" class="rounded-3xl border border-red-400/40 bg-red-500/10 p-5 text-sm text-red-100">
      {{ errorMessage }}
    </div>

    <section v-if="summary" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
      <article v-for="card in kpiCards" :key="card.label" class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 shadow-lg shadow-slate-950/30">
        <p class="text-xs uppercase tracking-[0.16em] text-slate-400">{{ card.label }}</p>
        <p class="mt-2 display-title text-3xl text-white">{{ card.value }}</p>
      </article>
    </section>

    <section v-if="summary" class="grid gap-4 lg:grid-cols-2">
      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Endpoints por sistema</h3>
        <canvas ref="endpointsBySystemCanvas" class="mt-3 h-64"></canvas>
      </article>
      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Endpoints por modulo</h3>
        <canvas ref="endpointsByModuleCanvas" class="mt-3 h-64"></canvas>
      </article>
      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Artefactos por tipo</h3>
        <canvas ref="artefactsByTypeCanvas" class="mt-3 h-64"></canvas>
      </article>
      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Endpoints por metodo HTTP</h3>
        <canvas ref="endpointsByMethodCanvas" class="mt-3 h-64"></canvas>
      </article>
    </section>

    <section v-if="summary" class="space-y-4">
      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Endpoints (muestra)</h3>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm text-slate-200">
            <thead class="text-left text-xs uppercase tracking-wide text-slate-400">
              <tr>
                <th class="pb-2 pr-3">Metodo</th>
                <th class="pb-2 pr-3">Path</th>
                <th class="pb-2 pr-3">Estado</th>
                <th class="pb-2 pr-3">Modulo</th>
                <th class="pb-2">Sistema</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in summary.tables.endpoints.slice(0, 12)" :key="row.public_id" class="border-t border-white/10">
                <td class="py-2 pr-3 font-semibold">{{ row.method }}</td>
                <td class="py-2 pr-3">{{ row.path }}</td>
                <td class="py-2 pr-3">{{ row.status }}</td>
                <td class="py-2 pr-3">{{ row.module_name }}</td>
                <td class="py-2">{{ row.system_name }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="rounded-2xl border border-white/10 bg-slate-950/55 p-4">
        <h3 class="display-title text-lg text-white">Artefactos (muestra)</h3>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm text-slate-200">
            <thead class="text-left text-xs uppercase tracking-wide text-slate-400">
              <tr>
                <th class="pb-2 pr-3">Tipo</th>
                <th class="pb-2 pr-3">Titulo</th>
                <th class="pb-2 pr-3">Modulo</th>
                <th class="pb-2">Sistema</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in summary.tables.artefacts.slice(0, 12)" :key="row.id" class="border-t border-white/10">
                <td class="py-2 pr-3">{{ row.type }}</td>
                <td class="py-2 pr-3">{{ row.title }}</td>
                <td class="py-2 pr-3">{{ row.module_name || '-' }}</td>
                <td class="py-2">{{ row.system_name || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
    </section>
  </section>
</template>

<script setup>
import { Chart, registerables } from 'chart.js';
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

Chart.register(...registerables);

const loadingSummary = ref(false);
const generatingPdf = ref(false);
const errorMessage = ref('');
const summary = ref(null);

const endpointsBySystemCanvas = ref(null);
const endpointsByModuleCanvas = ref(null);
const artefactsByTypeCanvas = ref(null);
const endpointsByMethodCanvas = ref(null);

let endpointsBySystemChart = null;
let endpointsByModuleChart = null;
let artefactsByTypeChart = null;
let endpointsByMethodChart = null;

const filters = reactive({
  system_id: '',
  module_id: '',
  status: '',
  date_from: '',
  date_to: '',
  theme: 'dark',
});

const filterCatalog = computed(() => summary.value?.filters || { systems: [], modules: [], statuses: [] });
const moduleOptions = computed(() => {
  if (!filters.system_id) {
    return filterCatalog.value.modules || [];
  }

  return (filterCatalog.value.modules || []).filter((module) => String(module.system_id) === String(filters.system_id));
});
const kpiCards = computed(() => {
  const kpis = summary.value?.kpis || { systems: 0, modules: 0, endpoints: 0, artefacts: 0 };

  return [
    { label: 'Total sistemas', value: kpis.systems },
    { label: 'Total modulos', value: kpis.modules },
    { label: 'Total endpoints', value: kpis.endpoints },
    { label: 'Total artefactos', value: kpis.artefacts },
  ];
});

const activeQueryParams = () => {
  const params = new URLSearchParams();

  if (filters.system_id) params.set('system_id', filters.system_id);
  if (filters.module_id) params.set('module_id', filters.module_id);
  if (filters.status) params.set('status', filters.status);
  if (filters.date_from) params.set('date_from', filters.date_from);
  if (filters.date_to) params.set('date_to', filters.date_to);

  return params;
};

const buildBarChart = (canvasRef, dataset, label, color) => {
  if (!canvasRef?.value) {
    return null;
  }

  return new Chart(canvasRef.value, {
    type: 'bar',
    data: {
      labels: dataset.map((item) => item.label),
      datasets: [
        {
          label,
          data: dataset.map((item) => item.value),
          backgroundColor: color,
          borderRadius: 8,
        },
      ],
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: { color: '#e2e8f0' },
        },
      },
      scales: {
        x: {
          ticks: { color: '#cbd5e1' },
          grid: { color: 'rgba(148, 163, 184, 0.15)' },
        },
        y: {
          beginAtZero: true,
          ticks: { color: '#cbd5e1', precision: 0 },
          grid: { color: 'rgba(148, 163, 184, 0.15)' },
        },
      },
    },
  });
};

const destroyCharts = () => {
  endpointsBySystemChart?.destroy();
  endpointsByModuleChart?.destroy();
  artefactsByTypeChart?.destroy();
  endpointsByMethodChart?.destroy();
  endpointsBySystemChart = null;
  endpointsByModuleChart = null;
  artefactsByTypeChart = null;
  endpointsByMethodChart = null;
};

const renderCharts = async () => {
  if (!summary.value) {
    return;
  }

  await nextTick();
  destroyCharts();

  endpointsBySystemChart = buildBarChart(
    endpointsBySystemCanvas,
    summary.value.charts.endpoints_by_system,
    'Endpoints',
    'rgba(34, 211, 238, 0.85)',
  );
  endpointsByModuleChart = buildBarChart(
    endpointsByModuleCanvas,
    summary.value.charts.endpoints_by_module,
    'Endpoints',
    'rgba(56, 189, 248, 0.85)',
  );
  artefactsByTypeChart = buildBarChart(
    artefactsByTypeCanvas,
    summary.value.charts.artefacts_by_type,
    'Artefactos',
    'rgba(52, 211, 153, 0.85)',
  );
  endpointsByMethodChart = buildBarChart(
    endpointsByMethodCanvas,
    summary.value.charts.endpoints_by_method,
    'Endpoints',
    'rgba(99, 102, 241, 0.85)',
  );
};

const loadSummary = async () => {
  loadingSummary.value = true;
  errorMessage.value = '';

  try {
    const params = activeQueryParams();
    const response = await fetch(`/api/v1/reports/summary?${params.toString()}`);

    if (!response.ok) {
      throw new Error('No fue posible cargar el resumen de reportes.');
    }

    summary.value = await response.json();
    await renderCharts();
  } catch (error) {
    summary.value = null;
    errorMessage.value = error instanceof Error ? error.message : 'Error inesperado al cargar reportes.';
  } finally {
    loadingSummary.value = false;
  }
};

const resetFilters = async () => {
  filters.system_id = '';
  filters.module_id = '';
  filters.status = '';
  filters.date_from = '';
  filters.date_to = '';
  filters.theme = 'dark';
  await loadSummary();
};

const generatePdf = async (disposition) => {
  generatingPdf.value = true;

  try {
    const payload = {
      ...Object.fromEntries(activeQueryParams().entries()),
      title: 'Reporte de catalogo AtlasHub',
      theme: filters.theme,
      disposition,
    };

    const response = await fetch('/api/v1/reports/generate-pdf', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/pdf',
      },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      throw new Error('No se pudo generar el PDF.');
    }

    const blob = await response.blob();
    const blobUrl = URL.createObjectURL(blob);

    if (disposition === 'inline') {
      window.open(blobUrl, '_blank', 'noopener,noreferrer');
      return;
    }

    const anchor = document.createElement('a');
    anchor.href = blobUrl;
    anchor.download = `atlashub-reporte-${new Date().toISOString().slice(0, 10)}.pdf`;
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'No fue posible generar el PDF.';
  } finally {
    generatingPdf.value = false;
  }
};

const toCsvLine = (values) => values.map((value) => `"${String(value ?? '').replaceAll('"', '""')}"`).join(',');

const downloadCsv = () => {
  if (!summary.value) {
    return;
  }

  const headers = ['Metodo', 'Path', 'Estado', 'Modulo', 'Sistema'];
  const rows = summary.value.tables.endpoints.map((row) => [
    row.method,
    row.path,
    row.status,
    row.module_name,
    row.system_name,
  ]);
  const csv = [toCsvLine(headers), ...rows.map((row) => toCsvLine(row))].join('\n');
  const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
  const blobUrl = URL.createObjectURL(blob);
  const anchor = document.createElement('a');
  anchor.href = blobUrl;
  anchor.download = `atlashub-reporte-${new Date().toISOString().slice(0, 10)}.csv`;
  document.body.appendChild(anchor);
  anchor.click();
  document.body.removeChild(anchor);
};

onMounted(loadSummary);
onBeforeUnmount(destroyCharts);
</script>


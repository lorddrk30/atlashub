<template>
  <section class="space-y-6 pb-12">
    <router-link
      :to="{ name: 'home', query: $route.query }"
      class="inline-flex items-center rounded-full border border-white/15 bg-white/[0.03] px-4 py-2 text-xs uppercase tracking-[0.2em] text-slate-200 transition hover:border-cyan-300/60 hover:text-cyan-100"
    >
      <- Volver
    </router-link>

    <article v-if="system" class="space-y-6 overflow-hidden rounded-[32px] border border-white/15 bg-white/[0.04] p-6 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl md:p-8">
      <header class="space-y-3">
        <p class="text-[11px] uppercase tracking-[0.3em] text-cyan-200/80">Sistema</p>
        <h2 class="display-title text-3xl font-semibold text-white md:text-4xl">{{ system.name }}</h2>
        <p class="max-w-3xl text-sm text-slate-300">{{ system.description || 'Sin descripcion disponible.' }}</p>
      </header>

      <section class="grid gap-3 rounded-2xl border border-white/10 bg-slate-950/60 p-4 md:grid-cols-2">
        <p class="text-sm text-slate-200"><strong class="text-white">Servidor PROD:</strong> {{ system.prod_server || '-' }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Servidor DEV:</strong> {{ system.dev_server || '-' }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Dominio interno:</strong> {{ system.internal_url || '-' }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Repositorio:</strong> {{ system.repository_url || system.gitlab_url || '-' }}</p>
      </section>
    </article>

    <section class="space-y-4 rounded-3xl border border-white/10 bg-slate-950/55 p-4 md:p-5">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Documentacion tecnica</p>
          <h3 class="display-title text-2xl text-white">Manuales y Documentacion</h3>
        </div>
      </div>

      <div class="grid gap-3 md:grid-cols-3">
        <select v-model="selectedModule" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Todos los modulos</option>
          <option v-for="module in modules" :key="module.id" :value="String(module.id)">{{ module.name }}</option>
        </select>
        <input
          v-model="query"
          type="search"
          placeholder="Buscar manual o guia..."
          class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none placeholder:text-slate-500 focus:border-cyan-300/70"
          @keydown.enter="loadDocuments"
        >
        <button class="rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:-translate-y-0.5 hover:brightness-110" @click="loadDocuments">Aplicar</button>
      </div>

      <p v-if="authError" class="rounded-2xl border border-amber-300/30 bg-amber-300/10 p-4 text-sm text-amber-100">
        Debes iniciar sesion para visualizar o descargar PDFs.
      </p>
      <p v-if="loadError" class="rounded-2xl border border-rose-300/30 bg-rose-300/10 p-4 text-sm text-rose-100">
        {{ loadError }}
      </p>

      <div v-if="loadingDocuments" class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-slate-300">Cargando documentos...</div>

      <div v-else-if="documents.length > 0" class="grid gap-3 md:grid-cols-2">
        <article
          v-for="doc in documents"
          :key="doc.id"
          class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 shadow-lg shadow-cyan-950/20 transition hover:-translate-y-0.5 hover:border-cyan-300/40"
        >
          <p class="text-[11px] uppercase tracking-[0.2em] text-cyan-200/75">{{ iconByType(doc.type) }} {{ labelByType(doc.type) }}</p>
          <h4 class="mt-2 text-lg font-semibold text-white">{{ doc.title }}</h4>
          <p class="mt-2 text-sm text-slate-300">{{ doc.description || 'Sin descripcion.' }}</p>
          <p class="mt-2 text-xs text-slate-400">{{ formatBytes(doc.size) }} · {{ doc.mime_type }}</p>
          <div class="mt-3 flex flex-wrap gap-2">
            <button
              class="rounded-lg border border-white/20 bg-white/[0.05] px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60"
              @click="openUrl(doc.view_url)"
            >
              Ver PDF
            </button>
            <button
              class="rounded-lg border border-white/20 bg-white/[0.05] px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60"
              @click="openUrl(doc.download_url)"
            >
              Descargar
            </button>
          </div>
        </article>
      </div>

      <p v-else class="rounded-2xl border border-dashed border-white/20 bg-slate-900/60 p-5 text-sm text-slate-400">
        No hay documentos para este sistema/modulo.
      </p>
    </section>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const system = ref(null);
const modules = ref([]);
const documents = ref([]);
const selectedModule = ref('');
const query = ref('');
const loadingDocuments = ref(false);
const authError = ref(false);
const loadError = ref('');

const loadSystem = async () => {
  const params = new URLSearchParams({
    system_id: String(route.params.systemId),
    per_category: '1',
  });

  const response = await fetch(`/api/v1/search?${params.toString()}`);
  const data = await response.json();
  system.value = data?.grouped?.systems?.[0] || null;
};

const loadModules = async () => {
  const response = await fetch('/api/v1/filters');
  const data = await response.json();
  modules.value = (data.modules || []).filter((module) => String(module.system_id) === String(route.params.systemId));
};

const loadDocuments = async () => {
  loadingDocuments.value = true;
  authError.value = false;
  loadError.value = '';

  try {
    const params = new URLSearchParams({ system_id: String(route.params.systemId) });

    if (selectedModule.value) {
      params.set('module_id', selectedModule.value);
    }

    if (query.value) {
      params.set('q', query.value);
    }

    const response = await fetch(`/api/v1/documents?${params.toString()}`, {
      headers: {
        Accept: 'application/json',
      },
      credentials: 'same-origin',
    });

    if (response.status === 401 || response.status === 403) {
      authError.value = true;
      documents.value = [];
      return;
    }

    if (!response.ok) {
      documents.value = [];
      loadError.value = 'No fue posible cargar documentos en este momento.';
      return;
    }

    const data = await response.json();
    documents.value = data.items || [];
  } catch (error) {
    documents.value = [];
    loadError.value = 'No fue posible cargar documentos en este momento.';
  } finally {
    loadingDocuments.value = false;
  }
};

const openUrl = (url) => {
  if (!url) {
    return;
  }

  window.open(url, '_blank', 'noopener,noreferrer');
};

const iconByType = (type) => {
  const map = {
    manual: 'MAN',
    guia: 'GUI',
    procedimiento: 'PRO',
    diagrama: 'DIA',
    politica: 'POL',
  };

  return map[type] || 'DOC';
};

const labelByType = (type) => {
  const map = {
    manual: 'Manual',
    guia: 'Guia',
    procedimiento: 'Procedimiento',
    diagrama: 'Diagrama',
    politica: 'Politica',
  };

  return map[type] || 'Documento';
};

const formatBytes = (bytes) => {
  if (!bytes || Number.isNaN(Number(bytes))) {
    return '0 B';
  }

  const value = Number(bytes);

  if (value < 1024) {
    return `${value} B`;
  }

  if (value < 1024 * 1024) {
    return `${(value / 1024).toFixed(1)} KB`;
  }

  return `${(value / (1024 * 1024)).toFixed(1)} MB`;
};

onMounted(async () => {
  await Promise.all([loadSystem(), loadModules()]);
  await loadDocuments();
});
</script>

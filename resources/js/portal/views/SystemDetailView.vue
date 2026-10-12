<template>
  <main class="mx-auto max-w-5xl space-y-8 pb-12">
    <router-link
      :to="{ name: 'home', query: catalogQuery }"
      class="inline-block text-sm text-cyan-200 hover:underline focus-visible:underline"
    >
      ← Volver al catálogo
    </router-link>

    <p v-if="loadingSystem" role="status" class="border-t border-slate-800 py-8 text-sm text-slate-300">Cargando sistema...</p>
    <div v-else-if="systemError" role="alert" class="border-t border-slate-800 py-8 text-sm text-slate-300">
      <p>No se pudo cargar el sistema.</p>
      <button type="button" class="mt-2 text-cyan-200 underline" @click="loadSystem">Reintentar</button>
    </div>
    <p v-else-if="!system" class="border-t border-slate-800 py-8 text-sm text-slate-300">Este sistema no está disponible en el catálogo.</p>

    <template v-else>
      <header class="border-b border-slate-800 pb-6">
        <h1 class="text-2xl font-semibold text-white md:text-3xl">{{ system.name }}</h1>
        <p v-if="system.description" class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ system.description }}</p>
      </header>

      <section aria-labelledby="environments-heading" class="space-y-4">
        <h2 id="environments-heading" class="text-xl font-semibold text-white">Ambientes</h2>
        <dl v-if="environments.length" class="divide-y divide-slate-800 overflow-hidden rounded-md border border-slate-800">
          <div v-for="environment in environments" :key="environment.label" class="grid gap-1 bg-slate-900 px-4 py-3 text-sm sm:grid-cols-[9rem_1fr] sm:gap-4">
            <dt class="font-medium text-slate-200">{{ environment.label }}</dt>
            <dd class="flex flex-wrap gap-x-4 gap-y-1 text-slate-300">
              <span v-if="environment.host" class="break-all">{{ environment.host }}</span>
              <span v-if="environment.ip" class="font-mono text-xs">{{ environment.ip }}</span>
            </dd>
          </div>
        </dl>
        <p v-else class="text-sm text-slate-400">No hay servidores registrados.</p>
      </section>

      <section v-if="system.responsibles?.length || system.user_areas?.length || links.length" aria-labelledby="contacts-heading" class="space-y-4">
        <h2 id="contacts-heading" class="text-xl font-semibold text-white">Contacto y enlaces</h2>
        <dl class="divide-y divide-slate-800 border-y border-slate-800 text-sm">
          <div v-if="system.responsibles?.length" class="grid gap-1 py-3 sm:grid-cols-[9rem_1fr] sm:gap-4"><dt class="font-medium text-slate-200">Responsables</dt><dd class="text-slate-300">{{ system.responsibles.join(', ') }}</dd></div>
          <div v-if="system.user_areas?.length" class="grid gap-1 py-3 sm:grid-cols-[9rem_1fr] sm:gap-4"><dt class="font-medium text-slate-200">Áreas usuarias</dt><dd class="text-slate-300">{{ system.user_areas.join(', ') }}</dd></div>
          <div v-for="link in links" :key="link.label" class="grid gap-1 py-3 sm:grid-cols-[9rem_1fr] sm:gap-4"><dt class="font-medium text-slate-200">{{ link.label }}</dt><dd class="min-w-0"><a :href="link.url" target="_blank" rel="noopener noreferrer" class="break-all text-cyan-200 hover:underline focus-visible:underline">{{ link.url }} <span class="sr-only">(abre en otra pestaña)</span></a></dd></div>
        </dl>
      </section>

      <section aria-labelledby="endpoints-heading" class="space-y-4 border-t border-slate-800 pt-6">
        <h2 id="endpoints-heading" class="text-xl font-semibold text-white">Endpoints publicados</h2>
        <form role="search" aria-label="Buscar endpoints de este sistema" class="flex flex-col gap-3 sm:flex-row sm:items-end" @submit.prevent="applyEndpointFilters">
          <div class="min-w-0 flex-1"><label for="endpoint-query" class="mb-1 block text-sm text-slate-300">Nombre o ruta</label><input id="endpoint-query" v-model="endpointQuery" type="search" placeholder="Ej. /api/v1/orders" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white placeholder:text-slate-400 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30"></div>
          <div v-if="modules.length" class="sm:w-48"><label for="endpoint-module" class="mb-1 block text-sm text-slate-300">Módulo</label><select id="endpoint-module" v-model="endpointModule" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-white focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30"><option value="">Todos</option><option v-for="module in modules" :key="module.id" :value="String(module.id)">{{ module.name }}</option></select></div>
          <button type="submit" class="rounded-md border border-cyan-300 bg-cyan-300 px-4 py-2.5 text-sm font-semibold text-slate-950 hover:bg-cyan-200">Buscar</button>
        </form>
        <p v-if="modulesError" role="alert" class="text-sm text-amber-200">No se pudieron cargar los módulos. <button type="button" class="underline" @click="loadModules">Reintentar</button></p>
        <p v-if="loadingEndpoints" role="status" class="py-4 text-sm text-slate-300">Cargando endpoints...</p>
        <div v-else-if="endpointsError" role="alert" class="py-4 text-sm text-slate-300"><p>No se pudieron cargar los endpoints.</p><button type="button" class="mt-2 text-cyan-200 underline" @click="loadEndpoints">Reintentar</button></div>
        <template v-else-if="endpointGroups.length">
          <div v-for="group in endpointGroups" :key="group.name" class="space-y-2">
            <h3 class="text-base font-medium text-slate-200">{{ group.name }}</h3>
            <ul class="divide-y divide-slate-800 rounded-md border border-slate-800 bg-slate-900">
              <li v-for="endpoint in group.items" :key="endpoint.public_id || `${endpoint.module_id}-${endpoint.method}-${endpoint.path}`">
                <router-link v-if="endpoint.public_id" :to="{ name: 'endpoint-detail', params: { publicId: endpoint.public_id }, query: { ...route.query, from_system: route.params.systemId } }" class="flex flex-col gap-1 px-4 py-3 hover:bg-slate-800/60 focus-visible:bg-slate-800/60 sm:flex-row sm:items-center sm:gap-4">
                  <span class="w-14 shrink-0 font-mono text-xs font-semibold text-cyan-200">{{ endpoint.method }}</span><span class="min-w-0 break-all font-mono text-xs text-slate-300">{{ endpoint.path }}</span><span class="text-sm text-white sm:ml-auto sm:text-right">{{ endpoint.name }}</span>
                </router-link>
                <div v-else class="flex flex-col gap-1 px-4 py-3 text-slate-400 sm:flex-row sm:items-center sm:gap-4">
                  <span class="w-14 shrink-0 font-mono text-xs">{{ endpoint.method }}</span><span class="min-w-0 break-all font-mono text-xs">{{ endpoint.path }}</span><span class="text-sm sm:ml-auto sm:text-right">{{ endpoint.name }} · Detalle no disponible</span>
                </div>
              </li>
            </ul>
          </div>
          <p v-if="endpoints.length === 50" class="text-sm text-slate-400">Se muestran hasta 50 endpoints. Refina la búsqueda para encontrar otros.</p>
        </template>
        <p v-else class="py-4 text-sm text-slate-400">{{ appliedEndpointQuery || appliedEndpointModule ? 'No hay endpoints que coincidan con esta búsqueda.' : 'Este sistema aún no tiene endpoints publicados.' }}</p>
      </section>

    <section aria-labelledby="documents-heading" class="space-y-4 border-t border-slate-800 pt-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 id="documents-heading" class="text-xl font-semibold text-white">Documentos</h2>
        </div>
      </div>

      <form role="search" aria-label="Buscar documentos de este sistema" class="grid gap-3 md:grid-cols-3" @submit.prevent="loadDocuments()">
        <label class="text-sm text-slate-300">Módulo
        <select v-model="selectedModule" class="mt-1 block w-full rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Todos los módulos</option>
          <option v-for="module in modules" :key="module.id" :value="String(module.id)">{{ module.name }}</option>
        </select>
        </label>
        <label class="text-sm text-slate-300">Título o descripción
        <input
          v-model="query"
          type="search"
          placeholder="Buscar manual o guía"
          class="mt-1 block w-full rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none placeholder:text-slate-400 focus:border-cyan-300"
        >
        </label>
        <button type="submit" class="self-end rounded-md border border-slate-600 bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:border-cyan-300/60">Buscar</button>
      </form>

      <p v-if="authError" role="alert" class="rounded-md border border-amber-300/30 bg-amber-300/10 p-4 text-sm text-amber-100">
        No tienes permiso para consultar estos documentos.
      </p>
      <p v-if="loadError" role="alert" class="rounded-md border border-rose-300/30 bg-rose-300/10 p-4 text-sm text-rose-100">
        {{ loadError }}
      </p>

      <div v-if="loadingDocuments" role="status" class="py-4 text-sm text-slate-300">Cargando documentos...</div>

      <div v-else-if="documents.length > 0" class="grid gap-3 md:grid-cols-2">
        <article
          v-for="doc in documents"
          :key="doc.id"
          class="rounded-md border border-slate-700 bg-slate-900 p-4 transition hover:border-cyan-300/40"
        >
          <p class="text-xs text-cyan-100">{{ iconByType(doc.type) }} {{ labelByType(doc.type) }}</p>
          <h4 class="mt-2 text-lg font-semibold text-white">{{ doc.title }}</h4>
          <p class="mt-2 text-sm text-slate-300">{{ doc.description || 'Sin descripcion.' }}</p>
          <p class="mt-2 text-xs text-slate-400">{{ formatBytes(doc.size) }} · {{ doc.mime_type }}</p>
          <div class="mt-3 flex flex-wrap gap-2">
            <a v-if="safeUrl(doc.view_url)" :href="doc.view_url" target="_blank" rel="noopener noreferrer" class="inline-block rounded-md border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60">
              Ver PDF
            </a>
            <a v-if="safeUrl(doc.download_url)" :href="doc.download_url" class="inline-block rounded-md border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60">
              Descargar
            </a>
          </div>
        </article>
      </div>

      <p v-else-if="!authError && !loadError" class="rounded-md border border-dashed border-slate-700 bg-slate-900 p-5 text-sm text-slate-400">
        No hay documentos disponibles para este sistema.
      </p>
      <button v-if="!loadingDocuments && documents.length < documentTotal" type="button" class="text-sm text-cyan-200 underline disabled:cursor-not-allowed disabled:text-slate-500" :disabled="documentFilterChangesPending" @click="loadDocuments(documentPage + 1, true)">Mostrar más documentos</button>
      <p v-if="documentFilterChangesPending && documents.length < documentTotal" class="text-xs text-slate-400">Aplica la búsqueda antes de cargar más documentos.</p>
    </section>
    </template>
  </main>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { clearDocumentResults, documentFiltersPending, readEndpointFilters, withEndpointFilters, withoutEndpointFilters } from '../utils/systemDetailState';

const route = useRoute();
const router = useRouter();
const system = ref(null);
const modules = ref([]);
const endpoints = ref([]);
const documents = ref([]);
const loadingSystem = ref(true);
const systemError = ref(false);
const loadingEndpoints = ref(true);
const endpointsError = ref(false);
const modulesError = ref(false);
const endpointQuery = ref('');
const endpointModule = ref('');
const appliedEndpointQuery = ref('');
const appliedEndpointModule = ref('');
const selectedModule = ref('');
const query = ref('');
const appliedDocumentModule = ref('');
const appliedDocumentQuery = ref('');
const loadingDocuments = ref(false);
const authError = ref(false);
const loadError = ref('');
const documentPage = ref(1);
const documentTotal = ref(0);
let generation = 0;
let endpointRequest = 0;
let documentRequest = 0;

const catalogQuery = computed(() => withoutEndpointFilters(route.query));
const documentFilterChangesPending = computed(() => documentFiltersPending(
  { query: query.value, module: selectedModule.value },
  { query: appliedDocumentQuery.value, module: appliedDocumentModule.value },
));

const environments = computed(() => [
  { label: 'Producción', host: system.value?.prod_server, ip: system.value?.prod_server_ip },
  { label: 'Pruebas', host: system.value?.uat_server, ip: system.value?.uat_server_ip },
  { label: 'Desarrollo', host: system.value?.dev_server, ip: system.value?.dev_server_ip },
].filter((item) => item.host || item.ip));

const safeUrl = (value) => {
  if (typeof value !== 'string' || !(/^(https?:\/\/|\/(?!\/))/i.test(value))) return false;
  try {
    return ['http:', 'https:'].includes(new URL(value, window.location.origin).protocol);
  } catch {
    return false;
  }
};
const links = computed(() => [
  { label: 'Dominio interno', url: system.value?.internal_url },
  { label: 'Dominio público', url: system.value?.public_url },
  { label: 'Repositorio', url: system.value?.repository_url },
].filter((item) => safeUrl(item.url)));
const endpointGroups = computed(() => {
  const groups = new Map();
  for (const endpoint of endpoints.value) {
    const name = endpoint.module?.name || 'Sin módulo';
    if (!groups.has(name)) groups.set(name, []);
    groups.get(name).push(endpoint);
  }
  return Array.from(groups, ([name, items]) => ({ name, items }));
});

const loadSystem = async () => {
  const current = generation;
  loadingSystem.value = true;
  systemError.value = false;
  const params = new URLSearchParams({
    system_id: String(route.params.systemId),
    per_category: '1',
  });

  try {
    const response = await fetch(`/api/v1/search?${params.toString()}`);
    if (response.status === 422) {
      if (current === generation) system.value = null;
      return;
    }
    if (!response.ok) throw new Error('System request failed');
    const data = await response.json();
    if (current === generation) system.value = data?.grouped?.systems?.[0] || null;
  } catch {
    if (current === generation) {
      system.value = null;
      systemError.value = true;
    }
  } finally {
    if (current === generation) loadingSystem.value = false;
  }
};

const loadModules = async () => {
  const current = generation;
  modulesError.value = false;
  try {
    const response = await fetch('/api/v1/filters');
    if (!response.ok) throw new Error('Module request failed');
    const data = await response.json();
    if (current === generation) modules.value = (data.modules || []).filter((module) => String(module.system_id) === String(route.params.systemId));
  } catch {
    if (current === generation) modulesError.value = true;
  }
};

const loadEndpoints = async () => {
  const current = generation;
  const request = ++endpointRequest;
  loadingEndpoints.value = true;
  endpointsError.value = false;
  try {
    const params = new URLSearchParams({ system_id: String(route.params.systemId), per_category: '50' });
    if (appliedEndpointQuery.value) params.set('q', appliedEndpointQuery.value);
    if (appliedEndpointModule.value) params.set('module_id', appliedEndpointModule.value);
    const response = await fetch(`/api/v1/search?${params.toString()}`);
    if (!response.ok) throw new Error('Endpoint request failed');
    const data = await response.json();
    if (current === generation && request === endpointRequest) endpoints.value = data?.grouped?.endpoints || [];
  } catch {
    if (current === generation && request === endpointRequest) {
      endpoints.value = [];
      endpointsError.value = true;
    }
  } finally {
    if (current === generation && request === endpointRequest) loadingEndpoints.value = false;
  }
};

const applyEndpointFilters = async () => {
  appliedEndpointQuery.value = endpointQuery.value.trim();
  appliedEndpointModule.value = endpointModule.value;
  await router.replace({
    name: 'system-detail',
    params: { systemId: route.params.systemId },
    query: withEndpointFilters(route.query, {
      query: appliedEndpointQuery.value,
      module: appliedEndpointModule.value,
    }),
  });
  await loadEndpoints();
};

const loadDocuments = async (page = 1, append = false) => {
  const current = generation;
  const request = ++documentRequest;
  if (!append) {
    appliedDocumentModule.value = selectedModule.value;
    appliedDocumentQuery.value = query.value.trim();
  }
  loadingDocuments.value = true;
  authError.value = false;
  loadError.value = '';

  try {
    const params = new URLSearchParams({ system_id: String(route.params.systemId), page: String(page) });

    if (appliedDocumentModule.value) {
      params.set('module_id', appliedDocumentModule.value);
    }

    if (appliedDocumentQuery.value) {
      params.set('q', appliedDocumentQuery.value);
    }

    const response = await fetch(`/api/v1/documents?${params.toString()}`, {
      headers: {
        Accept: 'application/json',
      },
      credentials: 'same-origin',
    });

    if (response.status === 401 || response.status === 403) {
      if (current === generation && request === documentRequest) {
        clearDocumentResults({ documents, documentPage, documentTotal });
        authError.value = true;
      }
      return;
    }

    if (!response.ok) {
      if (current === generation && request === documentRequest) {
        clearDocumentResults({ documents, documentPage, documentTotal });
        loadError.value = 'No fue posible cargar documentos en este momento.';
      }
      return;
    }

    const data = await response.json();
    if (current === generation && request === documentRequest) {
      documents.value = append ? [...documents.value, ...(data.items || [])] : (data.items || []);
      documentPage.value = data.meta?.current_page || page;
      documentTotal.value = data.meta?.total || 0;
    }
  } catch (error) {
    if (current === generation && request === documentRequest) {
      clearDocumentResults({ documents, documentPage, documentTotal });
      loadError.value = 'No fue posible cargar documentos en este momento.';
    }
  } finally {
    if (current === generation && request === documentRequest) loadingDocuments.value = false;
  }
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

watch(() => route.params.systemId, () => {
  generation += 1;
  system.value = null;
  modules.value = [];
  endpoints.value = [];
  clearDocumentResults({ documents, documentPage, documentTotal });
  const endpointFilters = readEndpointFilters(route.query);
  endpointQuery.value = endpointFilters.query;
  endpointModule.value = endpointFilters.module;
  appliedEndpointQuery.value = endpointFilters.query;
  appliedEndpointModule.value = endpointFilters.module;
  selectedModule.value = '';
  query.value = '';
  appliedDocumentModule.value = '';
  appliedDocumentQuery.value = '';
  Promise.all([loadSystem(), loadModules(), loadEndpoints(), loadDocuments()]);
}, { immediate: true });
</script>

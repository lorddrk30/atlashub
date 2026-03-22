<template>
  <section class="space-y-8 pb-8 md:space-y-10 md:pb-12">
    <div class="relative mx-auto max-w-4xl">
      <div class="atlas-grid pointer-events-none absolute inset-0 rounded-lg opacity-30" />
      <div class="relative overflow-hidden rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-sm md:p-8">
        <h1 class="text-3xl font-semibold leading-tight text-white md:text-4xl">Encuentra APIs internas en segundos</h1>
        <p class="mt-4 max-w-2xl text-sm text-slate-300 md:text-base">Busca por sistema, modulo, endpoint o artefacto. La experiencia esta pensada para discovery rapido, no para navegar menus administrativos.</p>

        <div class="mt-8 flex flex-col gap-3 md:flex-row">
          <label for="q" class="sr-only">Buscar en el portal</label>
          <input
            id="q"
            ref="searchInput"
            v-model="store.query.q"
            type="search"
            placeholder="api menu bebidas, inventario sucursal centro, repo pedidos mobile..."
            class="h-12 w-full rounded-md border border-slate-700 bg-slate-950 px-4 text-base text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30"
            @keydown.enter="submit"
          >
          <button class="h-12 rounded-md border border-cyan-300 bg-cyan-300 px-6 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300/40" @click="submit">Buscar</button>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
          <button
            v-for="term in quickTerms"
            :key="term"
            class="rounded-md border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-300 transition hover:border-cyan-300/50 hover:text-cyan-100"
            @click="applyQuickTerm(term)"
          >
            {{ term }}
          </button>
        </div>

        <p class="mt-4 text-xs text-slate-400">Atajo rapido: <kbd class="rounded border border-slate-700 bg-slate-950 px-2 py-1 text-[11px]">/</kbd></p>
      </div>
    </div>

    <section class="mx-auto max-w-5xl rounded-lg border border-slate-800 bg-slate-900 p-4 md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-sm font-semibold text-white">Filtros</p>
        </div>
        <div class="flex items-center gap-2">
          <button class="rounded-md border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 transition hover:border-cyan-300/50" @click="clearFilters">Limpiar</button>
          <button class="rounded-md border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 transition hover:border-cyan-300/50" @click="showFilters = !showFilters">
            {{ showFilters ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>

      <div v-if="showFilters" class="mt-4 grid gap-3 md:grid-cols-3">
        <select v-model="store.query.system_id" class="rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Sistema</option>
          <option v-for="item in store.filters.systems" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="store.query.module_id" class="rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Modulo</option>
          <option v-for="item in store.filters.modules" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="store.query.method" class="rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Metodo HTTP</option>
          <option v-for="item in store.filters.methods" :key="item" :value="item">{{ item }}</option>
        </select>
        <select v-model="store.query.authentication_type" class="rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Autenticacion</option>
          <option v-for="item in store.filters.authentication_types" :key="item" :value="item">{{ item }}</option>
        </select>
        <select v-model="store.query.artefact_type" class="rounded-md border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 outline-none focus:border-cyan-300">
          <option value="">Artefacto</option>
          <option v-for="item in store.filters.artefact_types" :key="item" :value="item">{{ item }}</option>
        </select>
        <button class="rounded-md border border-cyan-300 bg-cyan-300 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-cyan-200" @click="submit">Aplicar filtros</button>
      </div>
    </section>

    <div v-if="store.loading" class="mx-auto max-w-5xl rounded-lg border border-slate-800 bg-slate-900 p-6 text-sm text-slate-300">Buscando en el catalogo...</div>
    <div v-else-if="store.initialized && store.results.total === 0" class="mx-auto max-w-5xl rounded-lg border border-dashed border-slate-700 bg-slate-900 p-8 text-center text-sm text-slate-400">
      No encontramos resultados para esta combinacion. Prueba con otro termino.
    </div>

    <section v-for="section in sections" :key="section.key" class="mx-auto max-w-5xl space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white md:text-2xl">{{ section.label }}</h2>
        <span class="rounded-md border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-semibold text-cyan-100">{{ store.results.counts[section.key] || 0 }}</span>
      </div>

      <div v-if="section.items.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="item in section.items"
          :key="section.key + '-' + (item.public_id || item.id)"
          class="group relative overflow-hidden rounded-lg border border-slate-800 bg-slate-900 p-5 transition hover:border-cyan-300/35"
        >
          <template v-if="section.key === 'systems'">
            <p class="text-xs text-cyan-100">Sistema</p>
            <img
              v-if="item.home_preview_url"
              :src="item.home_preview_url"
              :alt="`Preview ${item.name}`"
              class="mt-3 h-28 w-full rounded-md border border-slate-700 object-cover"
            >
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-2 text-xs text-slate-400">PROD: {{ item.prod_server || '-' }}</p>
            <p class="mt-1 text-xs text-slate-400">DEV: {{ item.dev_server || '-' }}</p>
            <p class="mt-1 text-xs text-slate-400">Responsables: {{ joinTags(item.responsibles) }}</p>
            <p class="mt-1 text-xs text-slate-400">Areas usuarias: {{ joinTags(item.user_areas) }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
              <button v-if="item.internal_url" class="rounded-md border border-slate-700 bg-slate-950 px-2.5 py-1 text-[11px] text-slate-200 transition hover:border-cyan-300/50" @click="openUrl(item.internal_url)">Dominio interno</button>
              <button v-if="item.public_url" class="rounded-md border border-slate-700 bg-slate-950 px-2.5 py-1 text-[11px] text-slate-200 transition hover:border-cyan-300/50" @click="openUrl(item.public_url)">Dominio publico</button>
              <button
                v-if="item.repository_url || item.gitlab_url"
                class="rounded-md border border-slate-700 bg-slate-950 px-2.5 py-1 text-[11px] text-slate-200 transition hover:border-cyan-300/50"
                @click="openUrl(item.repository_url || item.gitlab_url)"
              >
                Repositorio
              </button>
              <button class="rounded-md border border-cyan-300/40 bg-cyan-300/10 px-2.5 py-1 text-[11px] text-cyan-100 transition hover:border-cyan-300/70" @click="goToSystem(item.id)">
                Ver sistema
              </button>
            </div>
          </template>

          <template v-else-if="section.key === 'modules'">
            <p class="text-xs text-cyan-100">Modulo</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-3 text-xs text-slate-400">{{ item.system?.name || 'Sistema sin definir' }}</p>
          </template>

          <template v-else-if="section.key === 'endpoints'">
            <p class="text-xs text-cyan-100">Endpoint</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300"><span class="rounded-md border border-cyan-300/30 bg-cyan-300/10 px-2 py-0.5 text-[11px] font-semibold text-cyan-100">{{ item.method }}</span></p>
            <p class="mt-2 text-sm text-slate-300">{{ shortText(item.path, 62) }}</p>
            <button class="mt-4 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white transition hover:border-cyan-300/50" @click="goToEndpoint(item.public_id)">Abrir mini swagger</button>
          </template>

          <template v-else-if="section.key === 'documents'">
            <p class="text-xs text-cyan-100">Documento</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.title }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ item.type }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ item.system?.name || 'Sistema sin definir' }}</p>
            <button class="mt-4 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white transition hover:border-cyan-300/50" @click="goToSystem(item.system_id)">Ver manuales</button>
          </template>

          <template v-else>
            <p class="text-xs text-cyan-100">Artefacto</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.title }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-3 text-xs text-slate-400">{{ item.type }}</p>
            <button class="mt-4 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white transition hover:border-cyan-300/50" @click="openUrl(item.url)">Abrir enlace</button>
          </template>
        </article>
      </div>

      <p v-else class="rounded-md border border-dashed border-slate-700 p-5 text-sm text-slate-400">Sin resultados en esta categoria.</p>
    </section>
  </section>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCatalogSearchStore } from '../stores/catalogSearch';

const searchInput = ref(null);
const showFilters = ref(false);
const route = useRoute();
const router = useRouter();
const store = useCatalogSearchStore();
const quickTerms = ['api menu bebidas', 'inventario sucursal centro', 'repo pedidos mobile'];

const sections = computed(() => [
  { key: 'systems', label: 'Sistemas', items: store.results.grouped.systems || [] },
  { key: 'modules', label: 'Modulos', items: store.results.grouped.modules || [] },
  { key: 'endpoints', label: 'Endpoints', items: store.results.grouped.endpoints || [] },
  { key: 'documents', label: 'Documentos', items: store.results.grouped.documents || [] },
  { key: 'artefacts', label: 'Artefactos', items: store.results.grouped.artefacts || [] },
]);

const syncUrl = async () => {
  await router.replace({
    name: 'home',
    query: {
      q: store.query.q || undefined,
      system_id: store.query.system_id || undefined,
      module_id: store.query.module_id || undefined,
      method: store.query.method || undefined,
      authentication_type: store.query.authentication_type || undefined,
      artefact_type: store.query.artefact_type || undefined,
    },
  });
};

const submit = async () => {
  await syncUrl();
  await store.search();
};

const clearFilters = async () => {
  store.query.q = '';
  store.query.system_id = '';
  store.query.module_id = '';
  store.query.method = '';
  store.query.authentication_type = '';
  store.query.artefact_type = '';
  await submit();
};

const applyQuickTerm = async (term) => {
  store.query.q = term;
  await submit();
};

const shortText = (value, limit = 96) => {
  if (!value) {
    return 'Sin descripcion disponible.';
  }

  const text = String(value).trim();

  if (text.length <= limit) {
    return text;
  }

  return `${text.slice(0, limit).trim()}...`;
};

const joinTags = (values) => {
  if (!Array.isArray(values) || values.length === 0) {
    return '-';
  }

  return values.join(', ');
};

const onShortcut = (event) => {
  if (event.key === '/' && route.name === 'home') {
    event.preventDefault();
    searchInput.value?.focus();
  }
};

const goToEndpoint = (publicId) => {
  if (!publicId) {
    return;
  }

  router.push({ name: 'endpoint-detail', params: { publicId }, query: route.query });
};

const goToSystem = (systemId) => {
  if (!systemId) {
    return;
  }

  router.push({ name: 'system-detail', params: { systemId }, query: route.query });
};

const openUrl = (url) => {
  if (!url) {
    return;
  }

  window.open(url, '_blank', 'noopener,noreferrer');
};

onMounted(async () => {
  window.addEventListener('keydown', onShortcut);
  store.hydrateQuery(route.query);
  await store.fetchFilters();
  await store.search();
});

onUnmounted(() => {
  window.removeEventListener('keydown', onShortcut);
});
</script>

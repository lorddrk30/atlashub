<template>
  <main class="mx-auto max-w-5xl space-y-6 pb-8 md:pb-12">
    <header class="catalog-heading">
      <h1 class="text-2xl font-semibold text-white md:text-3xl">Catálogo de sistemas y APIs</h1>
      <p class="mt-2 text-sm text-slate-300">Busca sistemas, endpoints, documentos y artefactos internos.</p>
    </header>

    <form role="search" class="space-y-2" @submit.prevent="submit">
      <label for="q" class="block text-sm font-medium text-slate-200">Buscar en el catálogo</label>
      <div class="catalog-search">
        <svg class="atlas-search-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4 4"/></svg>
        <input
          id="q"
          ref="searchInput"
          v-model="store.query.q"
          type="search"
          placeholder="Nombre, ruta, documento o repositorio"
        >
        <button type="submit" class="atlas-button atlas-button-primary">Buscar</button>
      </div>
    </form>

    <section aria-label="Filtros de búsqueda">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <button type="button" class="text-sm font-medium text-slate-200 hover:text-white" :aria-expanded="showFilters" aria-controls="catalog-filters" @click="showFilters = !showFilters">
          Filtros {{ showFilters ? '−' : '+' }}
        </button>
        <button v-if="hasActiveQuery" type="button" class="text-sm text-cyan-200 hover:text-cyan-100" @click="clearFilters">Limpiar búsqueda y filtros</button>
      </div>

      <div v-show="showFilters" id="catalog-filters" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
          <label for="filter-system" class="mb-1 block text-sm text-slate-300">Sistema</label>
          <select id="filter-system" v-model="store.query.system_id" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            <option value="">Todos</option>
            <option v-for="item in store.filters.systems" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
          </select>
        </div>
        <div>
          <label for="filter-module" class="mb-1 block text-sm text-slate-300">Módulo</label>
          <select id="filter-module" v-model="store.query.module_id" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            <option value="">Todos</option>
            <option v-for="item in store.filters.modules" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
          </select>
        </div>
        <div>
          <label for="filter-method" class="mb-1 block text-sm text-slate-300">Método HTTP</label>
          <select id="filter-method" v-model="store.query.method" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            <option value="">Todos</option>
            <option v-for="item in store.filters.methods" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>
        <div>
          <label for="filter-auth" class="mb-1 block text-sm text-slate-300">Autenticación</label>
          <select id="filter-auth" v-model="store.query.authentication_type" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            <option value="">Todas</option>
            <option v-for="item in store.filters.authentication_types" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>
        <div>
          <label for="filter-artefact" class="mb-1 block text-sm text-slate-300">Tipo de artefacto</label>
          <select id="filter-artefact" v-model="store.query.artefact_type" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-cyan-300 focus:ring-2 focus:ring-cyan-300/30">
            <option value="">Todos</option>
            <option v-for="item in store.filters.artefact_types" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="button" class="w-full rounded-md border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:border-cyan-300/50" @click="submit">Aplicar filtros</button>
        </div>
      </div>
      <p v-if="store.filtersError" class="mt-3 text-sm text-amber-200" role="alert">No se pudieron cargar los filtros. <button type="button" class="underline" @click="store.fetchFilters()">Reintentar</button></p>
    </section>

    <div v-if="store.loading || !store.initialized" class="border-t border-slate-800 py-8 text-sm text-slate-300" role="status">Cargando catálogo...</div>
    <div v-else-if="store.error" class="border-t border-slate-800 py-8 text-sm text-slate-300" role="alert">
      <p>No se pudo cargar el catálogo.</p>
      <button type="button" class="mt-3 text-cyan-200 underline" @click="store.search()">Reintentar</button>
    </div>
    <div v-else-if="store.results.total === 0" class="border-t border-slate-800 py-8 text-sm text-slate-300">
      <template v-if="hasSubmittedQuery">
        <h2 class="text-base font-semibold text-white">Sin resultados</h2>
        <p class="mt-2">No hay coincidencias para esta búsqueda. Prueba con otro término o quita los filtros.</p>
        <button type="button" class="mt-4 text-cyan-200 underline" @click="clearFilters">Mostrar todo el catálogo</button>
      </template>
      <template v-else>
        <h2 class="text-base font-semibold text-white">Aún no hay recursos publicados</h2>
        <p class="mt-2">Los sistemas y sus recursos aparecerán aquí cuando se publiquen.</p>
      </template>
    </div>

    <nav v-if="visibleSections.length > 1" class="catalog-section-nav" aria-label="Secciones del catálogo">
      <a v-for="section in visibleSections" :key="section.key" :href="`#catalog-${section.key}`">{{ section.label }} <span>{{ store.results.counts[section.key] || 0 }}</span></a>
    </nav>

    <section v-for="section in visibleSections" :id="`catalog-${section.key}`" :key="section.key" class="catalog-section space-y-4">
      <div class="catalog-section-title">
        <h2 class="text-white">{{ section.label }}</h2>
        <span>{{ store.results.counts[section.key] || 0 }}</span>
      </div>

      <div class="grid gap-4 md:grid-cols-2" :class="['systems', 'endpoints'].includes(section.key) ? '' : 'lg:grid-cols-3'">
        <article
          v-for="item in section.items"
          :key="section.key + '-' + (item.public_id || item.id)"
          class="catalog-card"
        >
          <template v-if="section.key === 'systems'">
            <div class="flex items-start gap-3">
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold"><button class="catalog-system-title" @click="goToSystem(item.id)">{{ item.name }}</button></h3>
                <p v-if="item.description" class="mt-2 text-sm text-slate-300">{{ shortText(item.description) }}</p>
              </div>
              <img
                v-if="item.home_preview_url && !failedPreviews.has(item.id)"
                :src="item.home_preview_url"
                :alt="`Vista previa de ${item.name}`"
                loading="lazy"
                @error="failedPreviews.add(item.id)"
                class="hidden h-16 w-24 shrink-0 rounded-md border border-slate-700 object-cover sm:block"
              >
            </div>
            <dl class="catalog-system-meta">
              <div v-if="item.prod_server || item.prod_server_ip" class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Producción</dt><dd class="min-w-0 break-words">{{ [item.prod_server, item.prod_server_ip].filter(Boolean).join(' · ') }}</dd></div>
              <div v-if="item.uat_server || item.uat_server_ip" class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Pruebas</dt><dd class="min-w-0 break-words">{{ [item.uat_server, item.uat_server_ip].filter(Boolean).join(' · ') }}</dd></div>
              <div v-if="item.dev_server || item.dev_server_ip" class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Desarrollo</dt><dd class="min-w-0 break-words">{{ [item.dev_server, item.dev_server_ip].filter(Boolean).join(' · ') }}</dd></div>
              <div v-if="item.responsibles?.length" class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Responsables</dt><dd class="min-w-0 break-words">{{ joinTags(item.responsibles) }}</dd></div>
              <div v-if="item.user_areas?.length" class="flex gap-2"><dt class="w-20 shrink-0 text-slate-400">Áreas</dt><dd class="min-w-0 break-words">{{ joinTags(item.user_areas) }}</dd></div>
            </dl>
            <div class="catalog-card-actions">
              <button v-if="item.internal_url" @click="openUrl(item.internal_url)">Dominio interno</button>
              <button v-if="item.public_url" @click="openUrl(item.public_url)">Dominio público</button>
              <button
                v-if="item.repository_url || item.gitlab_url"
                @click="openUrl(item.repository_url || item.gitlab_url)"
              >
                Repositorio
              </button>
              <button class="catalog-open" @click="goToSystem(item.id)">
                Ver sistema <span aria-hidden="true">→</span>
              </button>
            </div>
          </template>

          <template v-else-if="section.key === 'modules'">
            <p class="text-xs text-slate-400">Módulo</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-3 text-xs text-slate-400">{{ item.system?.name || 'Sistema sin definir' }}</p>
          </template>

          <template v-else-if="section.key === 'endpoints'">
            <button v-if="item.public_id" class="catalog-endpoint-row" @click="goToEndpoint(item.public_id)">
              <span class="http-method">{{ item.method }}</span>
              <span class="endpoint-copy"><span class="text-sm font-medium text-white">{{ item.name }}</span><code>{{ item.path }}</code></span>
              <span class="endpoint-arrow" aria-hidden="true">↗</span>
            </button>
            <div v-else class="catalog-endpoint-row"><span class="http-method">{{ item.method }}</span><span class="endpoint-copy"><span class="text-sm text-white">{{ item.name }}</span><code>{{ item.path }}</code></span></div>
            <p v-if="!item.public_id" class="mt-4 text-xs text-slate-400">Detalle no disponible</p>
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
    </section>
  </main>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCatalogSearchStore } from '../stores/catalogSearch';

const searchInput = ref(null);
const failedPreviews = ref(new Set());
const showFilters = ref(false);
const route = useRoute();
const router = useRouter();
const store = useCatalogSearchStore();

const sections = computed(() => [
  { key: 'systems', label: 'Sistemas', items: store.results.grouped.systems || [] },
  { key: 'modules', label: 'Módulos', items: store.results.grouped.modules || [] },
  { key: 'endpoints', label: 'Endpoints', items: store.results.grouped.endpoints || [] },
  { key: 'documents', label: 'Documentos', items: store.results.grouped.documents || [] },
  { key: 'artefacts', label: 'Artefactos', items: store.results.grouped.artefacts || [] },
]);
const visibleSections = computed(() => {
  if (!store.initialized || store.loading || store.error) {
    return [];
  }

  return sections.value.filter((section) => section.items.length > 0);
});
const hasActiveQuery = computed(() => Object.values(store.query).some((value) => String(value).trim() !== ''));
const hasSubmittedQuery = computed(() => Object.values(route.query).some((value) => String(value || '').trim() !== ''));

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
  const target = event.target;
  const isEditing = target instanceof HTMLElement && (target.isContentEditable || ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName));

  if (event.key === '/' && route.name === 'home' && !isEditing && !event.ctrlKey && !event.metaKey && !event.altKey) {
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
  await Promise.all([store.fetchFilters(), store.search()]);
});

onUnmounted(() => {
  window.removeEventListener('keydown', onShortcut);
});
</script>

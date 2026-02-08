<template>
  <section class="space-y-10 pb-10 md:space-y-12 md:pb-16">
    <div class="relative mx-auto max-w-4xl">
      <div class="atlas-grid pointer-events-none absolute inset-0 rounded-[32px] opacity-40" />
      <div class="relative overflow-hidden rounded-[32px] border border-white/15 bg-white/[0.04] p-6 shadow-2xl shadow-cyan-950/40 backdrop-blur-xl md:p-10">
        <p class="mb-4 text-[11px] uppercase tracking-[0.32em] text-cyan-200/80">Sistema -> Modulo -> Endpoint</p>
        <h1 class="display-title text-4xl font-semibold leading-tight text-white md:text-6xl">Encuentra APIs internas en segundos</h1>
        <p class="mt-4 max-w-2xl text-sm text-slate-300 md:text-base">Busca por sistema, modulo, endpoint o artefacto. La experiencia esta pensada para discovery rapido, no para navegar menus administrativos.</p>

        <div class="mt-8 flex flex-col gap-3 md:flex-row">
          <label for="q" class="sr-only">Buscar en el portal</label>
          <input
            id="q"
            ref="searchInput"
            v-model="store.query.q"
            type="search"
            placeholder="api menu bebidas, inventario sucursal centro, repo pedidos mobile..."
            class="h-16 w-full rounded-2xl border border-white/20 bg-slate-950/70 px-6 text-lg text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-300/70 focus:ring-2 focus:ring-cyan-300/40"
            @keydown.enter="submit"
          >
          <button class="h-16 rounded-2xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-8 text-base font-semibold text-slate-900 transition hover:-translate-y-0.5 hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-200" @click="submit">Buscar</button>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
          <button
            v-for="term in quickTerms"
            :key="term"
            class="rounded-full border border-white/15 bg-white/[0.04] px-3 py-1.5 text-xs text-slate-300 transition hover:-translate-y-0.5 hover:border-cyan-300/50 hover:text-cyan-100"
            @click="applyQuickTerm(term)"
          >
            {{ term }}
          </button>
        </div>

        <p class="mt-4 text-xs text-slate-400">Atajo rapido: <kbd class="rounded-md border border-white/20 bg-white/5 px-2 py-1 text-[11px]">/</kbd></p>
      </div>
    </div>

    <section class="mx-auto max-w-5xl rounded-3xl border border-white/10 bg-slate-950/50 p-4 backdrop-blur-xl md:p-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Busqueda avanzada</p>
          <h2 class="display-title text-xl text-white">Filtros</h2>
        </div>
        <div class="flex items-center gap-2">
          <button class="rounded-full border border-white/15 px-3 py-1.5 text-xs text-slate-200 transition hover:border-cyan-300/50" @click="clearFilters">Limpiar</button>
          <button class="rounded-full border border-white/15 px-3 py-1.5 text-xs text-slate-200 transition hover:border-cyan-300/50" @click="showFilters = !showFilters">
            {{ showFilters ? 'Ocultar' : 'Mostrar' }}
          </button>
        </div>
      </div>

      <div v-if="showFilters" class="mt-4 grid gap-3 md:grid-cols-3">
        <select v-model="store.query.system_id" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Sistema</option>
          <option v-for="item in store.filters.systems" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="store.query.module_id" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Modulo</option>
          <option v-for="item in store.filters.modules" :key="item.id" :value="String(item.id)">{{ item.name }}</option>
        </select>
        <select v-model="store.query.method" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Metodo HTTP</option>
          <option v-for="item in store.filters.methods" :key="item" :value="item">{{ item }}</option>
        </select>
        <select v-model="store.query.authentication_type" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Autenticacion</option>
          <option v-for="item in store.filters.authentication_types" :key="item" :value="item">{{ item }}</option>
        </select>
        <select v-model="store.query.artefact_type" class="rounded-xl border border-white/15 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 outline-none focus:border-cyan-300/70">
          <option value="">Artefacto</option>
          <option v-for="item in store.filters.artefact_types" :key="item" :value="item">{{ item }}</option>
        </select>
        <button class="rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-3 text-sm font-semibold text-slate-900 transition hover:-translate-y-0.5 hover:brightness-110" @click="submit">Aplicar filtros</button>
      </div>
    </section>

    <div v-if="store.loading" class="mx-auto max-w-5xl rounded-3xl border border-white/10 bg-white/[0.03] p-6 text-sm text-slate-300 shadow-xl shadow-cyan-950/20 animate-pulse">Buscando en el catalogo...</div>
    <div v-else-if="store.initialized && store.results.total === 0" class="mx-auto max-w-5xl rounded-3xl border border-dashed border-white/20 bg-slate-900/60 p-8 text-center text-sm text-slate-400">
      No encontramos resultados para esta combinacion. Prueba con otro termino.
    </div>

    <section v-for="section in sections" :key="section.key" class="mx-auto max-w-5xl space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="display-title text-2xl text-white md:text-3xl">{{ section.label }}</h2>
        <span class="rounded-full border border-cyan-300/30 bg-cyan-400/10 px-3 py-1 text-xs font-semibold tracking-wider text-cyan-100">{{ store.results.counts[section.key] || 0 }}</span>
      </div>

      <div v-if="section.items.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="item in section.items"
          :key="section.key + '-' + (item.public_id || item.id)"
          class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-slate-950/40 backdrop-blur-xl transition duration-300 hover:-translate-y-1.5 hover:border-cyan-300/35 hover:bg-cyan-300/[0.08]"
        >
          <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-cyan-300/10 blur-2xl transition group-hover:bg-cyan-300/25" />
          <template v-if="section.key === 'systems'">
            <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-200/75">Sistema</p>
            <h3 class="mt-2 text-xl font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
          </template>

          <template v-else-if="section.key === 'modules'">
            <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-200/75">Modulo</p>
            <h3 class="mt-2 text-xl font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-3 text-xs text-slate-400">{{ item.system?.name || 'Sistema sin definir' }}</p>
          </template>

          <template v-else-if="section.key === 'endpoints'">
            <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-200/75">Endpoint</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.name }}</h3>
            <p class="mt-3 text-sm text-slate-300"><span class="rounded-full border border-cyan-300/30 bg-cyan-300/10 px-2 py-0.5 text-[11px] font-semibold text-cyan-100">{{ item.method }}</span></p>
            <p class="mt-2 text-sm text-slate-300">{{ shortText(item.path, 62) }}</p>
            <button class="mt-4 rounded-xl border border-white/20 bg-white/[0.05] px-3 py-2 text-xs text-white transition hover:border-cyan-300/50 hover:bg-cyan-300/15" @click="goToEndpoint(item.public_id)">Abrir mini swagger</button>
          </template>

          <template v-else>
            <p class="text-[11px] uppercase tracking-[0.22em] text-cyan-200/75">Artefacto</p>
            <h3 class="mt-2 text-lg font-semibold text-white">{{ item.title }}</h3>
            <p class="mt-3 text-sm text-slate-300">{{ shortText(item.description) }}</p>
            <p class="mt-3 text-xs uppercase tracking-wider text-slate-400">{{ item.type }}</p>
            <button class="mt-4 rounded-xl border border-white/20 bg-white/[0.05] px-3 py-2 text-xs text-white transition hover:border-cyan-300/50 hover:bg-cyan-300/15" @click="openUrl(item.url)">Abrir enlace</button>
          </template>
        </article>
      </div>

      <p v-else class="rounded-2xl border border-dashed border-white/15 p-5 text-sm text-slate-400">Sin resultados en esta categoria.</p>
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

<template>
  <div class="atlas-shell">
    <a href="#main-content" class="atlas-skip-link">Saltar al contenido</a>
    <header class="atlas-header">
      <div class="atlas-header-inner">
        <router-link to="/" class="atlas-brand" aria-label="Ir al catálogo">
          <span v-if="!organization.logo_url" class="atlas-brand-mark">{{ organization.short_name }}</span>
          <img v-else :src="organization.logo_url" :alt="`Logo ${organization.name}`" class="atlas-brand-logo">
          <span class="atlas-brand-copy">
            <strong>{{ organization.name }}</strong>
            <span>{{ organization.tagline }}</span>
          </span>
        </router-link>
        <div class="atlas-account-actions">
          <a :href="organization.backoffice_url || '/backoffice'" class="atlas-button atlas-button-secondary">Administración</a>
          <form action="/logout" method="post">
            <input type="hidden" name="_token" :value="csrfToken">
            <button type="submit" class="atlas-button atlas-button-quiet">Salir</button>
          </form>
        </div>
      </div>
      <nav class="atlas-navigation" aria-label="Navegación principal">
        <router-link :to="{ name: 'home' }" :class="{ 'is-current': route.name !== 'reports-dashboard' }" :aria-current="route.name === 'home' ? 'page' : undefined">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Catálogo
        </router-link>
        <router-link :to="{ name: 'reports-dashboard' }" :class="{ 'is-current': route.name === 'reports-dashboard' }">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4v16h16M8 16v-4m5 4V8m5 8V5"/></svg>
          Reportes
        </router-link>
        <a href="/documentation">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h10l4 4v14H5zM14 3v5h5M8 12h8M8 16h6"/></svg>
          <span class="atlas-label-full">Documentación pública</span><span class="atlas-label-short">Docs públicas</span>
        </a>
      </nav>
    </header>
    <div id="main-content" class="atlas-content" tabindex="-1">
      <router-view />
    </div>
    <footer class="atlas-footer"><span>AtlasHub</span><span>Catálogo privado de tu organización</span></footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const organization = window.__ATLASHUB_CONFIG__ || {
  name: 'AtlasHub',
  short_name: 'AH',
  tagline: 'Portal de APIs internas',
  logo_url: null,
  backoffice_url: '/backoffice',
};

onMounted(() => {
  document.documentElement.classList.add('dark');
});
</script>

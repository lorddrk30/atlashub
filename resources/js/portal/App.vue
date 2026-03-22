<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
      <header class="mb-6 flex items-center justify-between gap-3 border-b border-slate-800 pb-4">
        <router-link to="/" class="inline-flex items-center gap-3">
          <span v-if="!organization.logo_url" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-cyan-200/30 bg-cyan-200/10 text-xs font-semibold text-cyan-100">{{ organization.short_name }}</span>
          <img v-else :src="organization.logo_url" :alt="`Logo ${organization.name}`" class="h-10 w-auto max-w-28 rounded-md border border-slate-700 bg-slate-900 object-contain px-2">
          <div>
            <p class="text-sm font-semibold text-slate-100">{{ organization.name }}</p>
            <p class="text-sm text-slate-300">{{ organization.tagline }}</p>
          </div>
        </router-link>
        <div class="flex items-center gap-2">
          <router-link
            :to="{ name: 'reports-dashboard' }"
            class="rounded-md border border-slate-700 bg-slate-900 px-4 py-2 text-xs text-slate-200 transition hover:border-cyan-300/50 hover:text-cyan-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300/40"
          >
            Reportes
          </router-link>
          <a :href="organization.backoffice_url || '/backoffice'" class="rounded-md border border-slate-700 bg-slate-900 px-4 py-2 text-xs text-slate-200 transition hover:border-cyan-300/50 hover:text-cyan-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300/40">Backoffice</a>
        </div>
      </header>
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

const organization = window.__ATLASHUB_CONFIG__ || {
  name: 'RikarCoffe',
  short_name: 'RC',
  tagline: 'Portal de APIs internas',
  logo_url: null,
  backoffice_url: '/backoffice',
};

onMounted(() => {
  document.documentElement.classList.add('dark');
});
</script>

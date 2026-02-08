<template>
  <div class="relative min-h-screen overflow-x-clip bg-[#040712] text-slate-100">
    <div class="pointer-events-none absolute -top-56 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-cyan-500/25 blur-[120px]" />
    <div class="pointer-events-none absolute right-[-120px] top-24 h-72 w-72 rounded-full bg-emerald-400/20 blur-[90px] animate-float-slow" />
    <div class="pointer-events-none absolute bottom-10 left-[-100px] h-64 w-64 rounded-full bg-sky-500/20 blur-[100px] animate-float-reverse" />

    <div class="relative mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
      <header class="mb-8 flex items-center justify-between gap-3">
        <router-link to="/" class="inline-flex items-center gap-3">
          <span v-if="!organization.logo_url" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-cyan-300/30 bg-cyan-400/15 text-xs font-semibold text-cyan-200 shadow-lg shadow-cyan-950/40">{{ organization.short_name }}</span>
          <img v-else :src="organization.logo_url" :alt="`Logo ${organization.name}`" class="h-10 w-10 rounded-2xl border border-cyan-300/30 bg-cyan-400/10 object-cover shadow-lg shadow-cyan-950/40">
          <div>
            <p class="text-[11px] uppercase tracking-[0.35em] text-cyan-200/80">{{ organization.name }}</p>
            <p class="display-title text-base tracking-tight text-white">{{ organization.tagline }}</p>
          </div>
        </router-link>
        <div class="flex items-center gap-2">
          <router-link
            :to="{ name: 'reports-dashboard' }"
            class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs uppercase tracking-[0.2em] text-slate-200 transition hover:-translate-y-0.5 hover:border-cyan-300/50 hover:bg-cyan-300/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300"
          >
            Reportes
          </router-link>
          <a :href="organization.backoffice_url || '/backoffice'" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs uppercase tracking-[0.2em] text-slate-200 transition hover:-translate-y-0.5 hover:border-cyan-300/50 hover:bg-cyan-300/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300">Backoffice</a>
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

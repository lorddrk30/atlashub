<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative min-h-screen overflow-hidden bg-[#050915] text-slate-100">
        <div class="pointer-events-none absolute -top-44 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-cyan-400/25 blur-[120px]" />
        <div class="pointer-events-none absolute bottom-[-90px] left-[-70px] h-72 w-72 rounded-full bg-emerald-400/20 blur-[100px]" />
        <div class="pointer-events-none absolute right-[-90px] top-28 h-72 w-72 rounded-full bg-sky-500/20 blur-[100px]" />

        <div class="relative mx-auto grid min-h-screen w-full max-w-6xl items-center gap-10 px-5 py-10 md:grid-cols-2 md:px-8">
            <section class="space-y-6">
                <Link href="/" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-cyan-300/35 bg-cyan-300/15 text-sm font-bold text-cyan-100">AH</span>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.32em] text-cyan-200/80">AtlasHub</p>
                        <p class="display-title text-lg text-white">Internal API Catalog</p>
                    </div>
                </Link>

                <div class="space-y-3">
                    <h1 class="display-title text-4xl font-semibold leading-tight text-white md:text-5xl">Bienvenido de vuelta</h1>
                    <p class="max-w-md text-sm text-slate-300 md:text-base">Accede al portal interno para buscar endpoints, módulos y artefactos técnicos en una experiencia moderna y rápida.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full border border-white/15 bg-white/[0.04] px-3 py-1.5 text-xs text-slate-300">RBAC</span>
                    <span class="rounded-full border border-white/15 bg-white/[0.04] px-3 py-1.5 text-xs text-slate-300">Sanctum</span>
                    <span class="rounded-full border border-white/15 bg-white/[0.04] px-3 py-1.5 text-xs text-slate-300">Laravel + Vue</span>
                </div>
            </section>

            <section class="w-full">
                <div class="rounded-[30px] border border-white/15 bg-white/[0.05] p-6 shadow-2xl shadow-slate-950/50 backdrop-blur-2xl md:p-8">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>

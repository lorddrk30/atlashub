<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="mx-auto grid min-h-screen w-full max-w-6xl items-center gap-8 px-5 py-8 md:grid-cols-2 md:px-8">
            <section class="space-y-5">
                <Link href="/" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-cyan-200/30 bg-cyan-200/10 text-sm font-semibold text-cyan-100">AH</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">AtlasHub</p>
                        <p class="text-sm text-slate-300">Internal API Catalog</p>
                    </div>
                </Link>

                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold leading-tight text-white md:text-4xl">Bienvenido de vuelta</h1>
                    <p class="max-w-md text-sm text-slate-300 md:text-base">Accede al portal interno para buscar endpoints, módulos y artefactos técnicos en una experiencia moderna y rápida.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-md border border-slate-700 bg-slate-900 px-3 py-1 text-xs text-slate-300">RBAC</span>
                    <span class="rounded-md border border-slate-700 bg-slate-900 px-3 py-1 text-xs text-slate-300">Sanctum</span>
                    <span class="rounded-md border border-slate-700 bg-slate-900 px-3 py-1 text-xs text-slate-300">Laravel + Vue</span>
                </div>
            </section>

            <section class="w-full">
                <div class="rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-sm md:p-8">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>

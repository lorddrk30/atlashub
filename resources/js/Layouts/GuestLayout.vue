<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <div class="mx-auto grid min-h-screen w-full max-w-6xl items-center gap-8 px-5 py-8 md:grid-cols-2 md:px-8">
            <section class="space-y-5">
                <a href="/documentation" class="inline-flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-cyan-200/30 bg-cyan-200/10 text-sm font-semibold text-cyan-100">AH</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-100">AtlasHub</p>
                        <p class="text-sm text-slate-300">Documentación pública</p>
                    </div>
                </a>

                <div class="space-y-2">
                    <h1 class="text-3xl font-semibold leading-tight text-white md:text-4xl">Acceso a la intranet</h1>
                    <p class="max-w-md text-sm text-slate-300 md:text-base">Consulta sistemas, direcciones internas, endpoints y reportes con tu cuenta. La documentación aprobada para todos está disponible sin iniciar sesión.</p>
                </div>

                <a href="/documentation" class="inline-block text-sm text-cyan-200 underline underline-offset-4 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-300">Ver documentación pública</a>
            </section>

            <section class="w-full">
                <div class="rounded-lg border border-slate-800 bg-slate-900 p-6 shadow-sm md:p-8">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>

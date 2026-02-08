<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesion" />

        <header class="mb-6 space-y-2">
            <p class="text-[11px] uppercase tracking-[0.26em] text-cyan-200/80">Acceso seguro</p>
            <h2 class="display-title text-3xl font-semibold text-white">Inicia sesion</h2>
            <p class="text-sm text-slate-300">Usa tus credenciales internas de AtlasHub.</p>
        </header>

        <div v-if="status" class="mb-4 rounded-xl border border-emerald-300/40 bg-emerald-300/15 px-3 py-2 text-sm font-medium text-emerald-100">
            {{ status }}
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo" class="text-sm font-medium text-slate-200" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-white/15 bg-slate-950/70 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300/70 focus:ring-cyan-300/45"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="tu@empresa.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Contrasena" class="text-sm font-medium text-slate-200" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-white/15 bg-slate-950/70 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300/70 focus:ring-cyan-300/45"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="********"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" class="border-white/25 bg-slate-900 text-cyan-300 focus:ring-cyan-300/45" />
                    <span class="ms-2 text-sm text-slate-300"
                        >Recordarme</span
                    >
                </label>
            </div>

            <div class="flex flex-col-reverse items-start justify-between gap-3 pt-2 sm:flex-row sm:items-center">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-slate-300 underline decoration-slate-500 underline-offset-4 transition hover:text-cyan-100 hover:decoration-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                >
                    Olvidaste tu contrasena?
                </Link>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-6 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-slate-900 transition hover:-translate-y-0.5 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-cyan-200 disabled:cursor-not-allowed disabled:opacity-60"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Entrar
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

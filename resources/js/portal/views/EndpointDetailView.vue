<template>
  <section class="space-y-5 pb-12">
    <router-link :to="{ name: 'home', query: $route.query }" class="inline-flex items-center rounded-full border border-white/15 bg-white/[0.03] px-4 py-2 text-xs uppercase tracking-[0.2em] text-slate-200 transition hover:border-cyan-300/60 hover:text-cyan-100"><- Volver</router-link>

    <article v-if="endpoint" class="space-y-7 overflow-hidden rounded-[32px] border border-white/15 bg-white/[0.04] p-5 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl md:p-8">
      <header class="space-y-3">
        <p class="text-[11px] uppercase tracking-[0.3em] text-cyan-200/80">Mini Swagger</p>
        <h2 class="display-title text-3xl font-semibold text-white md:text-4xl">{{ endpoint.name }}</h2>
        <p class="text-sm text-slate-200"><span class="rounded-full border border-cyan-300/35 bg-cyan-400/15 px-2.5 py-1 text-xs font-semibold text-cyan-100">{{ endpoint.method }}</span> <span class="ml-2 break-all">{{ endpoint.path }}</span></p>
        <div class="prose prose-sm max-w-none text-slate-300 prose-headings:text-white prose-strong:text-white prose-code:text-cyan-100" v-html="renderMarkdown(endpoint.description)" />
      </header>

      <section class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
        <h3 class="display-title text-lg text-white">Acciones rapidas</h3>
        <div class="mt-3 flex flex-wrap gap-2">
          <button class="rounded-xl bg-gradient-to-r from-cyan-400 to-emerald-400 px-4 py-2 text-sm font-semibold text-slate-900 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60" :disabled="!primaryUrl" @click="openUrl(primaryUrl)" aria-label="Abrir URL principal del endpoint">Abrir</button>
          <button class="rounded-xl border border-white/20 bg-white/[0.05] px-4 py-2 text-sm text-white transition hover:border-cyan-300/60 disabled:cursor-not-allowed disabled:opacity-60" :disabled="!primaryUrl" @click="copyUrl" aria-label="Copiar URL principal del endpoint">Copiar URL</button>
          <button class="rounded-xl border border-white/20 bg-white/[0.05] px-4 py-2 text-sm text-white transition hover:border-cyan-300/60 disabled:cursor-not-allowed disabled:opacity-60" :disabled="!curlCommand" @click="copyCurl" aria-label="Copiar comando cURL del endpoint">Copiar cURL</button>
          <a
            v-if="swaggerArtefact"
            :href="swaggerArtefact.url"
            target="_blank"
            rel="noopener noreferrer"
            class="rounded-xl border border-emerald-300/50 bg-emerald-300/15 px-4 py-2 text-sm text-emerald-100 transition hover:border-emerald-200"
            aria-label="Abrir Swagger del endpoint"
          >
            Ver Swagger
          </a>
        </div>
        <p v-if="feedback" class="mt-2 text-xs text-slate-400">{{ feedback }}</p>
      </section>

      <section class="grid gap-3 rounded-2xl border border-white/10 bg-slate-950/60 p-4 md:grid-cols-2">
        <p class="text-sm text-slate-200"><strong class="text-white">Autenticacion:</strong> {{ endpoint.authentication_type }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Sistema:</strong> {{ endpoint.module?.system?.name || 'N/A' }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Modulo:</strong> {{ endpoint.module?.name || 'N/A' }}</p>
        <p class="text-sm text-slate-200"><strong class="text-white">Estado:</strong> {{ endpoint.status }}</p>
      </section>

      <section>
        <h3 class="mb-2 display-title text-xl text-white">Parametros</h3>
        <div v-if="parameters.length > 0" class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/55">
          <table class="min-w-full text-sm text-slate-200">
            <thead class="bg-white/[0.06] text-slate-200">
              <tr>
                <th class="px-3 py-2 text-left">Nombre</th>
                <th class="px-3 py-2 text-left">Ubicacion</th>
                <th class="px-3 py-2 text-left">Tipo</th>
                <th class="px-3 py-2 text-left">Requerido</th>
                <th class="px-3 py-2 text-left">Descripcion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="param in parameters" :key="param.name + param.in" class="border-t border-white/10">
                <td class="px-3 py-2">{{ param.name }}</td>
                <td class="px-3 py-2">{{ param.in }}</td>
                <td class="px-3 py-2">{{ param.type }}</td>
                <td class="px-3 py-2">{{ param.required ? 'Si' : 'No' }}</td>
                <td class="px-3 py-2">{{ param.description }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="text-sm text-slate-400">Sin parametros documentados.</p>
      </section>

      <section class="grid gap-4 md:grid-cols-2">
        <div>
          <h3 class="mb-2 display-title text-xl text-white">Ejemplo request</h3>
          <pre class="overflow-x-auto rounded-xl bg-slate-950 p-4 text-xs text-slate-100">{{ formatJson(endpoint.request_example) }}</pre>
        </div>
        <div>
          <h3 class="mb-2 display-title text-xl text-white">Ejemplo response</h3>
          <pre class="overflow-x-auto rounded-xl bg-slate-950 p-4 text-xs text-slate-100">{{ formatJson(endpoint.response_example) }}</pre>
        </div>
      </section>

      <section>
        <h3 class="mb-2 display-title text-xl text-white">URLs por ambiente</h3>
        <div class="grid gap-2 md:grid-cols-2">
          <div
            v-for="(url, env) in (endpoint.urls || {})"
            :key="env"
            class="rounded-xl border border-white/10 bg-slate-950/55 px-3 py-2 text-sm"
          >
            <p class="mb-2 text-xs uppercase tracking-wide text-slate-400">{{ env }}</p>
            <div class="flex gap-2">
              <button class="rounded-lg border border-white/20 bg-white/[0.06] px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60" @click="openUrl(url)">Abrir</button>
              <button class="rounded-lg border border-white/20 bg-white/[0.06] px-3 py-1.5 text-xs text-white transition hover:border-cyan-300/60" @click="copyText(url, `URL ${env.toUpperCase()} copiada`)">Copiar</button>
            </div>
          </div>
        </div>
      </section>

      <section>
        <h3 class="mb-2 display-title text-xl text-white">Artefactos</h3>
        <div v-if="artefacts.length > 0" class="grid gap-2 md:grid-cols-2">
          <a
            v-for="item in artefacts"
            :key="item.id"
            :href="item.url"
            target="_blank"
            rel="noopener noreferrer"
            class="rounded-xl border border-white/10 bg-slate-950/55 px-3 py-2 text-sm text-slate-100 transition hover:border-cyan-300/50 hover:bg-cyan-300/[0.08]"
          >
            {{ item.title }} ({{ item.type }})
          </a>
        </div>
        <p v-else class="text-sm text-slate-400">Sin artefactos asociados.</p>
      </section>
    </article>

    <p v-else-if="loading" class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 text-sm text-slate-300">Cargando endpoint...</p>
    <p v-else class="rounded-2xl border border-dashed border-white/20 bg-slate-900/50 p-5 text-sm text-slate-400">Endpoint no encontrado.</p>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import DOMPurify from 'dompurify';
import { marked } from 'marked';

const route = useRoute();
const loading = ref(false);
const endpoint = ref(null);
const feedback = ref('');

const parameters = computed(() => endpoint.value?.parameters || []);
const artefacts = computed(() => endpoint.value?.artefacts || []);
const swaggerArtefact = computed(() => artefacts.value.find((item) => item.type === 'swagger') || null);
const primaryUrl = computed(() => {
  const urls = endpoint.value?.urls || {};

  return urls.prod || urls.uat || urls.dev || Object.values(urls)[0] || '';
});
const curlCommand = computed(() => {
  if (!endpoint.value || !primaryUrl.value) {
    return '';
  }

  let command = `curl -X ${endpoint.value.method} "${primaryUrl.value}"`;

  if (endpoint.value.authentication_type && endpoint.value.authentication_type !== 'none') {
    command += ' -H "Authorization: Bearer <token>"';
  }

  const method = String(endpoint.value.method || '').toUpperCase();
  const hasBody = !['GET', 'DELETE'].includes(method)
    && endpoint.value.request_example
    && Object.keys(endpoint.value.request_example).length > 0;

  if (hasBody) {
    const payload = JSON.stringify(endpoint.value.request_example).replace(/"/g, '\\"');
    command += ' -H "Content-Type: application/json"';
    command += ` --data "${payload}"`;
  }

  return command;
});

const loadEndpoint = async () => {
  loading.value = true;

  try {
    const response = await fetch(`/api/v1/endpoints/${route.params.publicId}`);

    if (!response.ok) {
      endpoint.value = null;
      return;
    }

    const data = await response.json();
    endpoint.value = data.item;
  } finally {
    loading.value = false;
  }
};

const formatJson = (value) => {
  if (!value) {
    return '{}';
  }

  return JSON.stringify(value, null, 2);
};

const renderMarkdown = (value) => {
  if (!value) {
    return '<p>Sin descripcion.</p>';
  }

  return DOMPurify.sanitize(marked.parse(String(value)));
};

const copyText = async (text, message = 'Copiado al portapapeles') => {
  if (!text) {
    return;
  }

  if (navigator.clipboard?.writeText) {
    await navigator.clipboard.writeText(text);
    feedback.value = message;

    return;
  }

  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.setAttribute('readonly', '');
  textarea.style.position = 'absolute';
  textarea.style.left = '-9999px';
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);
  feedback.value = message;
};

const copyUrl = async () => {
  await copyText(primaryUrl.value, 'URL principal copiada');
};

const copyCurl = async () => {
  await copyText(curlCommand.value, 'Comando cURL copiado');
};

const openUrl = (url) => {
  if (!url) {
    return;
  }

  window.open(url, '_blank', 'noopener,noreferrer');
};

onMounted(loadEndpoint);
</script>

<template>
    <div class="atlas-auth">
        <header class="atlas-auth-header">
            <a href="/documentation" class="atlas-brand">
                <span class="atlas-brand-mark">AH</span>
                <span class="atlas-brand-copy"><strong>AtlasHub</strong><span>Sistemas y documentación</span></span>
            </a>
            <a href="/documentation" class="atlas-button atlas-button-quiet">Documentación pública <span aria-hidden="true">↗</span></a>
        </header>
        <main class="atlas-auth-content">
            <section class="atlas-auth-intro">
                <h1 class="text-white">Acceso a la intranet</h1>
                <p>Consulta los sistemas, ambientes y APIs de tu organización. Sus responsables y documentación, en el mismo lugar.</p>
                <a href="/documentation" class="atlas-auth-public">¿Buscas documentación pública? <span aria-hidden="true">→</span></a>
            </section>
            <section class="atlas-auth-card" aria-label="Acceso a la cuenta">
                <slot />
            </section>
        </main>
        <footer class="atlas-auth-footer">Los recursos internos requieren una cuenta de tu organización.</footer>
    </div>
</template>

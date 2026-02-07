<script setup>
import { useTemplateStore } from "@/stores/template";
import { onMounted, onUnmounted } from "vue";

import BaseLayout from "@/layouts/BaseLayout.vue";

// Main store
const store = useTemplateStore();

// Set default elements for this layout
store.setLayout({
  header: true,
  sidebar: false,
  sideOverlay: false,
  footer: false,
});

// Set various template options for this layout variation
store.headerStyle({ mode: "dark" });
store.mainContent({ mode: "" });

const bodyClass = "layout-landing";
onMounted(() => {
  document.body.classList.add(bodyClass);
});
onUnmounted(() => {
  document.body.classList.remove(bodyClass);
});
</script>

<template>
  <BaseLayout>
    <!-- Header Content Left -->
    <template #header-content-left>
      <div class="landing-header__left d-flex align-items-center gap-3">
        <RouterLink
          :to="{ name: 'landing' }"
          class="landing-header__logo"
        >
          Escuela
          <span class="landing-header__logo--light">Presente</span>
        </RouterLink>
        <span class="landing-header__version">v{{ store.app.version }}</span>
      </div>
    </template>

    <!-- Header Content Right -->
    <template #header-content-right>
      <RouterLink :to="{ name: 'login' }" class="landing-header__login">
        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>
        <span class="d-none d-sm-inline">Iniciar Sesión</span>
      </RouterLink>
    </template>
  </BaseLayout>
</template>

<style scoped>
/* Landing header: transparent over hero, only when this layout is active */
:deep(#page-header) {
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

:deep(#page-header .content-header) {
  padding-top: 0.35rem;
  padding-bottom: 0.35rem;
}

.landing-header__logo {
  font-size: 1.35rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  color: #fff !important;
  text-decoration: none;
  transition: opacity 0.2s;
}

.landing-header__logo:hover {
  color: #fff !important;
  opacity: 0.92;
}

.landing-header__logo--light {
  font-weight: 400;
  opacity: 0.95;
}

.landing-header__version {
  font-size: 0.7rem;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.6);
  padding: 0.2rem 0.5rem;
  border-radius: 9999px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.15);
}

.landing-header__login {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: #fff !important;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 0.5rem;
  text-decoration: none;
  transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
}

.landing-header__login:hover {
  color: #fff !important;
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.45);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>

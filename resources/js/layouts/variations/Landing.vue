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
store.headerStyle({ mode: "" });
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
          class="landing-header__logo-link"
        >
          <img
            src="/assets/fonts/image/main-logo.png"
            alt="Escuela Presente Logo"
            class="landing-header__logo-img"
          />
        </RouterLink>
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
  position: fixed !important;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: rgba(238, 238, 216, 0.45) !important;
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
}

:deep(#page-header .content-header) {
  padding-top: 0.35rem;
  padding-bottom: 0.35rem;
}

.landing-header__logo-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: transform 0.2s ease;
}

.landing-header__logo-link:hover {
  transform: scale(1.05);
}

.landing-header__logo-img {
  height: 200px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 12px rgba(0, 0, 0, 0.3));
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
  padding: 0.5rem 1.25rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: #fff !important;
  background: #0ea5e9;
  border: none;
  border-radius: 0.5rem;
  text-decoration: none;
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
  transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
}

.landing-header__login:hover {
  color: #fff !important;
  background: #0284c7;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.5);
}

/* Responsive logo sizes */
@media (max-width: 991.98px) {
  .landing-header__logo-img {
    height: 56px;
  }
}

@media (max-width: 575.98px) {
  .landing-header__logo-img {
    height: 48px;
  }

  .landing-header__version {
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
  }
}
</style>

<style>
/* Global styles for landing layout - make content start from top */
.layout-landing #main-container {
  padding-top: 0 !important;
}

.layout-landing #page-container {
  padding-top: 0 !important;
}
</style>

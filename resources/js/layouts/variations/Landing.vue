<script setup>
import { useTemplateStore } from "@/stores/template";

import BaseLayout from "@/layouts/BaseLayout.vue";

// Main store
const store = useTemplateStore();
const showButton = ref(false);

// Set default elements for this layout
store.setLayout({
  header: true,
  sidebar: false,
  sideOverlay: false,
  footer: false,
});

// Set various template options for this layout variation
store.headerStyle({ mode: "dark" });
store.mainContent({ mode: "boxed" });
</script>

<template>
  <BaseLayout>
    <!-- Header Content Left -->
    <!-- Using the available v-slot, we can override the default Side Overlay content from layouts/partials/Header.vue -->
    <template #header-content-left>
      <div class="d-flex align-items-center">
        <!-- Logo -->
        <RouterLink
          :to="{ name: 'landing' }"
          class="fw-bold fs-lg tracking-wider text-dual me-2"
        >
          Escuela
          <span class="fw-normal">Presente</span>
        </RouterLink>
        <!-- END Logo -->

        <!-- Version -->
        <div
          class="fs-xs fw-medium py-1 px-3 rounded-pill bg-body-dark text-dark"
        >
          v{{ store.app.version }}
        </div>
      </div>
    </template>
    <!-- END Header Content Left -->

    <!-- Header Content Right -->
    <!-- Using the available v-slot, we can override the default Side Overlay content from layouts/partials/Header.vue -->
    <template #header-content-right>
      <router-link :to="{ name: 'dashboard' }">
        <button
          type="button"
          class="btn btn-success"
          v-click-ripple
          v-if="showButton"
        >
          <i class="fa-solid fa-arrow-right-to-bracket"></i>
          <span class="d-none d-sm-inline-block ms-2">Iniciar Sesión</span>
        </button>
      </router-link>
    </template>
    <!-- END Header Content Right -->
  </BaseLayout>
</template>

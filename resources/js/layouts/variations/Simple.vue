<script setup>
import { useTemplateStore } from "@/stores/template";

import BaseLayout from "@/layouts/BaseLayout.vue";
import ThemeToggle from "@/components/ThemeToggle.vue";

// Main store
const store = useTemplateStore();

// Set default elements for this layout
store.setLayout({
  header: false,
  sidebar: false,
  sideOverlay: false,
  footer: false,
});

// Set various template options for this layout variation
store.mainContent({ mode: "full" });
</script>

<template>
  <BaseLayout />
  <ThemeToggle variant="ghost" class="ep-theme-toggle--floating" />
</template>

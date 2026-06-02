<template>
  <button
    type="button"
    class="ep-theme-toggle"
    :class="buttonClass"
    :title="label"
    :aria-label="label"
    @click="onToggle"
  >
    <i class="fa-solid" :class="iconClass" aria-hidden="true"></i>
    <span v-if="showLabel" class="ep-theme-toggle__label">{{ shortLabel }}</span>
  </button>
</template>

<script setup>
import { computed } from "vue";
import { useTemplateStore } from "@/stores/template";
import { persistThemePreference, syncDocumentTheme } from "@/services/themePreference";

const props = defineProps({
  variant: {
    type: String,
    default: "default",
    validator: (v) => ["default", "ghost", "landing"].includes(v),
  },
  showLabel: {
    type: Boolean,
    default: false,
  },
});

const store = useTemplateStore();

const isDark = computed(() => store.settings.darkMode);

const iconClass = computed(() =>
  isDark.value ? "fa-sun" : "fa-moon"
);

const label = computed(() =>
  isDark.value ? "Activar modo claro" : "Activar modo oscuro"
);

const shortLabel = computed(() => (isDark.value ? "Claro" : "Oscuro"));

const buttonClass = computed(() => ({
  "ep-theme-toggle--ghost": props.variant === "ghost",
  "ep-theme-toggle--landing": props.variant === "landing",
}));

function onToggle() {
  if (store.settings.darkModeSystem) {
    store.darkModeSystem({ mode: "off" });
  }
  store.darkMode({ mode: "toggle" });
  persistThemePreference(store.settings);
  syncDocumentTheme(store.settings.darkMode);
}
</script>

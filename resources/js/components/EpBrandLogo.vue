<template>
  <RouterLink
    :to="to"
    class="ep-brand"
    :class="rootClass"
    aria-label="Escuela Presente — inicio"
  >
    <span v-if="variant === 'mini'" class="ep-brand__glyph" aria-hidden="true">EP</span>
    <template v-else>
      <span class="ep-brand__icon" aria-hidden="true">
        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="32" height="32" rx="9" :fill="`url(#${gradId})`" />
          <text
            x="16"
            y="21"
            text-anchor="middle"
            fill="#fff"
            font-size="11"
            font-weight="700"
            font-family="system-ui, sans-serif"
          >
            EP
          </text>
          <defs>
            <linearGradient :id="gradId" x1="4" y1="4" x2="28" y2="28" gradientUnits="userSpaceOnUse">
              <stop stop-color="#635bff" />
              <stop offset="1" stop-color="#00d4ff" />
            </linearGradient>
          </defs>
        </svg>
      </span>
      <span class="ep-brand__wordmark">
        <span class="ep-brand__primary">Escuela</span>
        <span class="ep-brand__secondary">Presente</span>
      </span>
    </template>
  </RouterLink>
</template>

<script setup>
import { computed } from "vue";

let brandGradCounter = 0;
const gradId = `ep-brand-grad-${++brandGradCounter}`;

const props = defineProps({
  to: {
    type: [String, Object],
    default: () => ({ name: "landing" }),
  },
  variant: {
    type: String,
    default: "full",
    validator: (v) => ["full", "mini"].includes(v),
  },
  linkClass: {
    type: [String, Array, Object],
    default: "",
  },
});

const rootClass = computed(() => [
  props.linkClass,
  {
    "ep-brand--mini": props.variant === "mini",
  },
]);
</script>

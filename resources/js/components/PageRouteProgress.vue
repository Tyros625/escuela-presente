<script setup>
import { routeProgressVisible, routeProgressValue } from "@/router/pageTransition";
</script>

<template>
  <Teleport to="body">
    <div
      v-show="routeProgressVisible"
      class="ep-route-progress"
      role="status"
      aria-live="polite"
      aria-label="Cargando página"
    >
      <div class="ep-route-progress__panel">
        <div class="ep-route-progress__track">
          <div
            class="ep-route-progress__bar"
            :class="{ 'ep-route-progress__bar--done': routeProgressValue >= 100 }"
            :style="{ width: `${routeProgressValue}%` }"
          />
        </div>
      </div>
    </div>
  </Teleport>
</template>

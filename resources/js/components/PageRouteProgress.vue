<script setup>
import EpSplashEmblem from "@/components/EpSplashEmblem.vue";
import { routeProgressVisible } from "@/router/pageTransition";
</script>

<template>
  <Teleport to="body">
    <Transition name="ep-route-overlay">
      <div
        v-if="routeProgressVisible"
        class="ep-route-progress"
        role="status"
        aria-live="polite"
        aria-busy="true"
        aria-label="Cargando página"
      >
        <div class="ep-route-progress__backdrop" aria-hidden="true"></div>
        <div class="ep-route-progress__stage">
          <EpSplashEmblem />
          <div class="ep-splash-bar" role="progressbar" aria-hidden="true"></div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

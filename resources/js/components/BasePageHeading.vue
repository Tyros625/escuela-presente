<script setup>
defineProps({
  title: {
    type: String,
    description: "The title of page heading section",
  },
  subtitle: {
    type: String,
    description: "The subtitle of page heading section",
  },
});
</script>

<template>
  <div class="ep-page-heading">
    <div class="content content-full">
      <slot>
        <div
          v-if="title || $slots.extra"
          class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-3"
        >
          <div class="flex-grow-1">
            <h1 v-if="title" class="ep-page-title">{{ title }}</h1>
            <p v-if="subtitle" class="ep-page-subtitle">{{ subtitle }}</p>
          </div>
          <div v-if="$slots.extra" class="flex-shrink-0 d-flex flex-wrap gap-2">
            <slot name="extra"></slot>
          </div>
        </div>
      </slot>
    </div>
  </div>
</template>

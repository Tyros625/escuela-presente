<template>
  <div :class="`spinner-border text-primary`" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: "text-primary",
    description: "The url of the image to show",
  },
});
</script>

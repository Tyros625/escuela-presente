<template>
  <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
    <div class="flex-grow-1 me-3">
      <b>Errores:</b>
      <ul>
        <li v-for="error in errors" :key="error">
          {{ error }}
        </li>
      </ul>
    </div>
    <div class="flex-shrink-0">
      <i class="fa fa-fw fa-times-circle"></i>
    </div>
  </div>
</template>

<script setup>
defineProps({
  errors: Array,
});
</script>

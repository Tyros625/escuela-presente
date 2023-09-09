<script setup></script>

<template>
  <!-- Hero -->
  <div class="hero bg-body-extra-light">
    <div class="content content-full text-center">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
          <i class="fa fa-6x fa-solid fa-circle-exclamation text-danger"></i>
          <h1 class="fw=bold mt-3 mb-2">¡Error en el Pago!</h1>
          <p class="fs-lg fw-medium text-muted mb-4">
            Hubo algun error al momento de procesar tu pago.
          </p>
          <RouterLink
            :to="{ name: 'landing' }"
            class="btn btn-primary px-4 py-3"
            v-click-ripple
          >
            <i class="fa fa-fw fa-arrow-left ms-1 opacity-50"></i>
            Regresar
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
  <!-- END Hero -->
</template>

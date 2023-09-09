<script setup>
const route = useRoute();

onMounted(() => {
  updatePayment();
});

async function updatePayment() {
  try {
    const { data } = await api.post(`/payments/update`, route.query);
    console.log(data);
  } catch (error) {
    console.log(error);
  }
}
</script>

<template>
  <!-- Hero -->
  <div class="hero bg-body-extra-light">
    <div class="content content-full text-center">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
          <i class="fa fa-6x fa-regular fa-exclamation text-warning"></i>
          <h1 class="fw=bold mt-3 mb-2">¡Pago Pendiente!</h1>
          <p class="fs-lg fw-medium text-muted mb-4">
            Estaremos a la espera de tu pago. Una vez lo realices, te llegará un
            mail de confirmación.
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

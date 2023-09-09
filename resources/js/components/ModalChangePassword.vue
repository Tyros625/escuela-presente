<script setup>
import { useUserStore } from "@/stores/user";
const userStore = useUserStore();
const isLoading = ref(false);
const form = ref({
  user_id: userStore.getUser.id,
  password: null,
  password_confirmation: null,
});

function onSubmit() {
  isLoading.value = true;
  api
    .put(`/change-password`, form.value)
    .then(() => {
      Toast.fire({
        icon: "success",
        title: "Contraseña Cambiada Correctamente",
      });
      isLoading.value = false;
      modalHide(`modal-password`);
    })
    .catch((err) => {
      Toast.fire({
        icon: "error",
        title: "Error",
      });
      isLoading.value = false;
    });
  isLoading.value = false;
}

function modalHide(modalName) {
  var myModal = bootstrap.Modal.getOrCreateInstance(
    document.getElementById(modalName)
  );
  myModal.hide();
}
</script>

<template>
  <form @submit.prevent="onSubmit">
    <!-- Modal -->
    <div
      class="modal fade"
      id="modal-password"
      tabindex="-1"
      aria-labelledby="modal-password-label"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modal-password-label">
              Cambiar Contraseña
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label class="form-label">Nueva Contraseña</label>
                <input
                  type="password"
                  class="form-control"
                  v-model="form.password"
                  :disabled="isLoading"
                />
              </div>
              <div class="col-md-12">
                <label class="form-label">Confirmar Contraseña</label>
                <input
                  type="password"
                  class="form-control"
                  v-model="form.password_confirmation"
                  :disabled="isLoading"
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cerrar
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
              <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
              <i class="fa-solid fa-floppy-disk" v-else></i>
              Cambiar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<style lang="scss" scoped></style>

<template>
  <BasePageHeading title="Roles">
    <template #extra>
      <router-link :to="{ name: 'roles' }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-arrow-left-long opacity-50 me-1"></i>
          Regresar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <ErrorsView v-if="errors.length" :errors="errors" />
    <BaseBlock title="Agregar Rol">
      <form @submit.prevent="addData" class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" v-model="form.name" />
          </div>
          <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
const initialForm = {
  name: "",
};
const form = reactive({ ...initialForm });
const router = useRouter();
const errors = ref([]);

function addData() {
  api
    .post(`/roles`, form)
    .then((res) => {
      Object.assign(form, res.data.data);
      Toast.fire({
        icon: "success",
        title: "Guardado Correctamente",
      });
      router.push({ name: "roles" });
    })
    .catch((err) => {
      Toast.fire({
        icon: "error",
        title: err.response.data.message,
      });
    });
}
</script>

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
    <BaseBlock :title="`Editar Rol #${route.params.id}`">
      <form @submit.prevent="updateData" class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input
              type="text"
              class="form-control"
              v-model="form.name"
              :disabled="form.is_locked"
            />
            <small v-if="form.is_locked" class="text-muted">
              Los roles fijos (Administrador, Docente, Estudiante, Padre/Tutor) no pueden renombrarse.
            </small>
          </div>
          <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary" :disabled="form.is_locked">
              Actualizar
            </button>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
onMounted(() => {
  editData();
});

const initialForm = {
  name: "",
  is_locked: false,
};
const form = reactive({ ...initialForm });
const route = useRoute();
const router = useRouter();
const errors = ref([]);

function editData() {
  api
    .get(`/roles/${route.params.id}`)
    .then((res) => {
      if (res.status === 200) {
        Object.assign(form, res.data);
      }
    })
    .catch((err) => {
      console.log(err.response.data.errors);
    });
}

function updateData() {
  api
    .put(`/roles/${route.params.id}`, form)
    .then(() => {
      Object.assign(form, initialForm);
      router.push({ name: "roles" });
      Toast.fire({
        icon: "success",
        title: "Actualizado Correctamente",
      });
    })
    .catch((err) => {
      Toast.fire({
        icon: "error",
        title: err.response.data.message,
      });
    });
}
</script>

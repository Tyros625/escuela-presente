<template>
  <BasePageHeading title="Usuarios">
    <template #extra>
      <router-link :to="{ name: 'users' }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-arrow-left-long opacity-50 me-1"></i>
          Regresar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <ErrorsView v-if="errors.length" :errors="errors" />
    <BaseBlock title="Agregar Usuarios">
      <form @submit.prevent="addData" class="mb-4">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select class="form-select" v-model="form.role_id">
              <option v-for="rol in roles" :value="rol.id" :key="rol.id">
                {{ rol.name }}
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nombres</label>
            <input type="text" class="form-control" v-model="form.name" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" v-model="form.email" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Contraseña</label>
            <input
              type="password"
              class="form-control"
              v-model="form.password"
            />
          </div>
          <div class="col-md-6">
            <label class="form-label">Confirmar Contraseña</label>
            <input
              type="password"
              class="form-control"
              v-model="form.password_confirmation"
            />
          </div>
          <div class="col-md-6">
            <label class="form-label">Activo</label>
            <select class="form-select" v-model="form.active">
              <option :value="true">Si</option>
              <option :value="false">No</option>
            </select>
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
onMounted(() => {
  listRoles();
});

const initialForm = {
  role_id: 3,
  code: "",
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  active: true,
};
const form = reactive({ ...initialForm });
const router = useRouter();
const roles = ref([]);
const errors = ref([]);

function listRoles() {
  api
    .get(`/lists/roles`)
    .then((res) => {
      if (res.status === 200) {
        roles.value = res.data;
      }
    })
    .catch((err) => {
      console.log(err.response.data.errors);
    });
}

function addData() {
  api
    .post(`/users`, form)
    .then((res) => {
      Object.assign(form, res.data.data);
      router.push({ name: "users" });
      Toast.fire({
        icon: "success",
        title: "Guardado Correctamente",
      });
    })
    .catch((err) => {
      errors.value = [];
      Object.getOwnPropertyNames(err.data.errors).forEach(function (val) {
        err.data.errors[val].forEach((element) => {
          errors.value.push(element);
        });
      });

      Toast.fire({
        icon: "error",
        title: "Error",
      });
    });
}
</script>

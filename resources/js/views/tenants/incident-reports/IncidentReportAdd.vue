<template>
  <BasePageHeading title="Incidente">
    <template #extra>
      <router-link :to="{ name: 'reports.incidents' }">
        <button type="button" class="btn btn-alt-primary" v-click-ripple>
          <i class="fa fa-arrow-left-long opacity-50 me-1"></i>
          Regresar
        </button>
      </router-link>
    </template>
  </BasePageHeading>

  <div class="content">
    <ErrorsView v-if="errors.length" :errors="errors" />
    <BaseBlock title="Nuevo Incidente">
      <form @submit.prevent="saveData" class="mb-4">
        <div class="row">
          <div class="col-sm-6 mt-2">
            <label class="form-label">Estudiante</label>
            <VueSelect
              v-model="form.student_id"
              :options="students"
              label="name"
              :reduce="(option) => option.id"
              placeholder="Elige un valor..."
              :disabled="isLoading"
            />
          </div>
          <div class="col-sm-6 mt-2">
            <label class="form-label">Incidente</label>
            <VueSelect
              v-model="form.incident_id"
              :options="incidents"
              label="description"
              :reduce="(option) => option.id"
              placeholder="Elige un valor..."
              :disabled="isLoading"
            />
          </div>
          <div class="col-sm-6 mt-2">
            <label class="form-label">Profesor</label>
            <VueSelect
              v-model="form.teacher_id"
              :options="teachers"
              label="display_name"
              :reduce="(option) => option.id"
              placeholder="Elige un valor..."
              :disabled="isLoading"
            />
          </div>
          <div class="col-sm-6 mt-2">
            <label class="form-label">Especialidad</label>
            <VueSelect
              v-model="form.specialty_id"
              :options="specialties"
              label="description"
              :reduce="(option) => option.id"
              placeholder="Elige un valor..."
              :disabled="isLoading"
            />
          </div>
          <div class="col-sm-6 mt-2">
            <label class="form-label">Observación</label>
            <textarea
              class="form-control"
              v-model="form.observations"
              rows="5"
              placeholder="Textarea content.."
              :disabled="isLoading"
            />
          </div>
          <div class="col-sm-6 mt-2">
            <div class="row">
              <div class="col-sm-12">
                <label class="form-label">Foto</label>
                <input
                  class="form-control"
                  type="file"
                  accept="image/*"
                  @change="onChangeFile"
                />
              </div>
              <div class="col-sm-12 mt-2" v-if="form.photo">
                <img style="max-width: 100%" :src="form.photo" />
              </div>
            </div>
          </div>
          <div class="col-sm-12 mt-2 text-end">
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
              <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
              <i class="fa-solid fa-floppy-disk" v-else></i>
              Guardar
            </button>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
import VueSelect from "vue-select";

onMounted(() => {
  getStudents();
  getIncidents();
  getSpecialties();
  getTeachers();
});

const initialForm = {
  student_id: null,
  incident_id: null,
  teacher_id: null,
  specialty_id: null,
  photo: null,
  observations: null,
};
const form = reactive({ ...initialForm });
const router = useRouter();
const errors = ref([]);
const students = ref([]);
const incidents = ref([]);
const specialties = ref([]);
const teachers = ref([]);
const photo = ref();
const isLoading = ref(false);

const getStudents = async () => {
  const { data } = await api.get(`/lists/students`);
  students.value = data;
};

const getIncidents = async () => {
  const { data } = await api.get(`/lists/incidents`);
  incidents.value = data;
};

const getSpecialties = async () => {
  const { data } = await api.get(`/lists/specialties`);
  specialties.value = data;
};

const getTeachers = async () => {
  const { data } = await api.get(`/lists/teachers`);
  teachers.value = data;
};

function saveData() {
  api
    .post(`/incident-reports`, form)
    .then(() => {
      Object.assign(form, initialForm);
      router.push({ name: "reports.incidents" });
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

function onChangeFile(e) {
  isLoading.value = true;
  photo.value = e.target.files[0];
  uploadToDO();
}

async function uploadToDO() {
  isLoading.value = true;
  const formData = new FormData();
  formData.append("file", photo.value);

  try {
    const { data } = await api.post(`upload-file`, formData);
    form.photo = data;
    isLoading.value = false;
  } catch (error) {
    console.error(error);
    isLoading.value = false;
  }
}
</script>

<style lang="scss">
// Vue Select + Custom overrides
@import "vue-select/dist/vue-select.css";
@import "@/assets/scss/vendor/vue-select";
</style>

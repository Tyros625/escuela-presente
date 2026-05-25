<script setup>
import moment from "moment";

const ENROLLMENT_LENGTH = 7;

const isLoading = ref(false);
const isSearching = ref(false);
const registeringEnrollment = ref(null);
const enrollment = ref("");
const searchName = ref("");
const searchResults = ref([]);
const dataFetched = ref(null);
const hasSearched = ref(false);
const enrollmentInputRef = ref(null);

function formatDate(date) {
  return moment(date).format("DD/MM/YY");
}

function formatHour(date) {
  return moment(date).format("h:mm a");
}

function fullName(student) {
  return `${student.last_name_father} ${student.last_name_mother}, ${student.name}`;
}

function gradeGroup(student) {
  const grade = student.grade ?? "";
  const group = student.group ?? "";
  return grade && group ? `${grade}-${group}` : grade || group || "—";
}

function focusEnrollmentInput() {
  nextTick(() => {
    enrollmentInputRef.value?.focus();
  });
}

onMounted(() => {
  focusEnrollmentInput();
});

async function registerAssist(enrollmentCode) {
  const code = String(enrollmentCode ?? "").trim();
  if (!code || registeringEnrollment.value) {
    return;
  }

  registeringEnrollment.value = code;
  isLoading.value = true;

  try {
    const res = await api.post(`/assists/${code}`, {}, { showErrors: false });
    dataFetched.value = res.data.data;
    enrollment.value = "";
    Toast.fire({
      icon: "success",
      title: "Guardado Correctamente",
    });
    focusEnrollmentInput();
  } catch (err) {
    Toast.fire({
      icon: "error",
      title: err?.data?.message ?? "No se pudo registrar la asistencia",
    });
    enrollment.value = "";
    focusEnrollmentInput();
  } finally {
    isLoading.value = false;
    registeringEnrollment.value = null;
  }
}

function onEnrollmentInput() {
  const value = enrollment.value?.trim() ?? "";
  if (value.length === ENROLLMENT_LENGTH) {
    registerAssist(value);
  }
}

async function searchStudents() {
  const term = searchName.value?.trim() ?? "";
  if (!term) {
    Toast.fire({
      icon: "warning",
      title: "Ingrese un nombre o apellido para buscar",
    });
    return;
  }

  isSearching.value = true;
  hasSearched.value = true;

  try {
    const res = await api.get("/students", {
      params: { name: term },
    });
    searchResults.value = res.data.data ?? [];
    if (searchResults.value.length === 0) {
      Toast.fire({
        icon: "info",
        title: "No se encontraron estudiantes",
      });
    }
  } catch {
    searchResults.value = [];
  } finally {
    isSearching.value = false;
  }
}
</script>

<template>
  <!-- Hero -->
  <BasePageHeading title="Registrar Asistencia" />
  <!-- END Hero -->

  <!-- Page Content -->
  <div class="content">
    <BaseBlock title="Nueva Asistencia" content-full>
      <div class="row">
        <div class="col-lg-4">
          <p class="fs-sm text-muted">Ingresar matrícula</p>
        </div>
        <div class="col-lg-8 space-y-2">
          <form class="row row-cols-lg-auto g-3 align-items-center" @submit.prevent>
            <div class="col-12">
              <label class="visually-hidden">Matrícula</label>
              <input
                ref="enrollmentInputRef"
                type="text"
                class="form-control"
                placeholder="Matrícula"
                v-model="enrollment"
                :disabled="isLoading"
                autocomplete="off"
                @input="onEnrollmentInput"
              />
            </div>
          </form>
        </div>
      </div>

      <div v-if="dataFetched" class="mt-4">
        <div class="text-center">
          <h3 class="text-success m-5">
            <b>Se registro la asistencia correctamente</b>
          </h3>
        </div>
        <div class="container">
          <div class="row">
            <div class="col-sm">
              <h3 class="text-primary">
                <b>Alumno:</b>
                {{ dataFetched.student.name }}
                {{ dataFetched.student.last_name_father }}
                {{ dataFetched.student.last_name_mother }}
              </h3>
            </div>
            <div class="col-sm">
              <h5><b>Curp:</b> {{ dataFetched.student.curp }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Grado:</b> {{ dataFetched.student.grade }}</h5>
            </div>
          </div>

          <div class="row">
            <div class="col-sm">
              <h5><b>Grupo:</b> {{ dataFetched.student.group }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Fecha:</b> {{ formatDate(dataFetched.created_at) }}</h5>
            </div>
            <div class="col-sm">
              <h5><b>Hora:</b> {{ formatHour(dataFetched.created_at) }}</h5>
            </div>
          </div>
        </div>
      </div>

      <hr class="my-4" />

      <div class="row">
        <div class="col-lg-4">
          <p class="fs-sm text-muted">Registrar por apellido</p>
        </div>
        <div class="col-lg-8">
          <form class="row row-cols-lg-auto g-3 align-items-center" @submit.prevent="searchStudents">
            <div class="col-12 col-md-auto flex-grow-1">
              <label class="visually-hidden">Buscar por nombre</label>
              <input
                type="text"
                class="form-control"
                placeholder="Buscar por nombre o apellido"
                v-model="searchName"
                :disabled="isSearching"
                autocomplete="off"
                @keyup.enter="searchStudents"
              />
            </div>
            <div class="col-12 col-md-auto">
              <button type="submit" class="btn btn-primary" :disabled="isSearching">
                <i class="fa fa-cog fa-spin me-1" v-if="isSearching"></i>
                <i class="fa-solid fa-magnifying-glass me-1" v-else></i>
                Consultar
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="hasSearched" class="mt-4">
        <div v-if="searchResults.length === 0" class="text-center text-muted py-4">
          No se encontraron estudiantes con ese criterio de búsqueda.
        </div>
        <div v-else class="table-responsive">
          <table class="table table-striped table-vcenter">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Matrícula</th>
                <th scope="col">Apellidos, Nombres</th>
                <th scope="col">Edad</th>
                <th scope="col">Grado/Grupo</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(student, index) in searchResults" :key="student.id">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ student.enrollment }}</td>
                <td>{{ fullName(student) }}</td>
                <td>{{ student.age }} años</td>
                <td>{{ gradeGroup(student) }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-alt-primary"
                    :disabled="registeringEnrollment === student.enrollment || isLoading"
                    @click="registerAssist(student.enrollment)"
                  >
                    <i
                      class="fa fa-cog fa-spin me-1"
                      v-if="registeringEnrollment === student.enrollment"
                    ></i>
                    Registrar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseBlock>
  </div>
  <!-- END Page Content -->
</template>

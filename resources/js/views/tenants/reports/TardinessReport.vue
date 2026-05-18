<template>
  <BasePageHeading
    title="Retardo"
    subtitle="Reporte de alumnos que llegaron tarde"
  />

  <div class="content">
    <BaseBlock title="Filtros de Búsqueda">
      <form @submit.prevent="submit" class="mb-4">
        <p class="text-muted mb-3">
          En esta sección se muestran únicamente los alumnos que registraron
          asistencia después del horario de retardo configurado.
        </p>

        <div class="row mb-3">
          <div class="col-md-12">
            <label class="form-label d-block mb-2">Turno</label>
            <div
              v-for="shift in shifts"
              :key="shift.value"
              class="form-check form-check-inline"
            >
              <input
                :id="`shift-${shift.value}`"
                v-model="form.shift"
                class="form-check-input"
                type="radio"
                :value="shift.value"
              />
              <label class="form-check-label" :for="`shift-${shift.value}`">
                {{ shift.label }}:
              </label>
            </div>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" v-model="form.date" />
          </div>
        </div>

        <div class="col-md-12 mt-3">
          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            <i class="fa fa-cog fa-spin" v-if="isLoading"></i>
            <i class="fa-solid fa-magnifying-glass" v-else></i>
            Consultar
          </button>
          <button
            type="button"
            class="btn btn-secondary ms-2"
            :disabled="isLoading || !records.length"
            @click="printData"
          >
            <i class="fa-solid fa-print"></i>
            Imprimir
          </button>
        </div>

        <div class="col-md-12 mt-4" id="printTable">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter">
              <thead class="table-primary">
                <tr>
                  <th>Matrícula</th>
                  <th>Nombre completo</th>
                  <th>Grado</th>
                  <th>Grupo</th>
                  <th>Hora</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!records.length && searched">
                  <td colspan="5" class="text-center text-muted">
                    No hay alumnos en retardo para los filtros seleccionados.
                  </td>
                </tr>
                <tr v-for="item in records" :key="item.id">
                  <td>{{ item.enrollment }}</td>
                  <td>{{ item.full_name }}</td>
                  <td>{{ item.grade }}</td>
                  <td>{{ item.group }}</td>
                  <td>{{ item.time }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </BaseBlock>
  </div>
</template>

<script setup>
import api from "@/services/api";
import { changeTimeZone } from "@/services/timezone";

const shifts = [
  { value: "morning", label: "Matutino" },
  { value: "afternoon", label: "Vespertino" },
  { value: "fulltime", label: "Tiempo completo" },
];

const form = reactive({
  shift: "morning",
  date: changeTimeZone("America/Mexico_City").slice(0, 10),
});

const records = ref([]);
const isLoading = ref(false);
const searched = ref(false);

function submit() {
  isLoading.value = true;
  searched.value = true;

  api
    .get("assists/tardiness", { params: form })
    .then((res) => {
      records.value = res.data.data ?? [];
    })
    .catch((err) => {
      records.value = [];
      const message =
        err.response?.data?.message ||
        "No se pudo consultar el reporte de retardo.";
      Toast.fire({ icon: "error", title: message });
    })
    .finally(() => {
      isLoading.value = false;
    });
}

function printData() {
  window.print();
}
</script>

<style scoped>
@media print {
  .btn,
  .form-label,
  .form-check,
  input,
  BasePageHeading {
    display: none !important;
  }
}
</style>

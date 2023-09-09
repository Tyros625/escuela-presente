<script setup>
import { useStudentStore } from "@/stores/student";

const studentStore = useStudentStore();

onMounted(async () => {
  window.addEventListener("load", window.print());
});
</script>

<template>
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <h2>Reporte de Asistencias</h2>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <b>Grado:</b> {{ studentStore.form_print.grade }}<br>
        <b>Grupo:</b> {{ studentStore.form_print.group }}
      </div>
    </div>

    <div class="row">
      <div class="col-12 table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Estudiante</th>
              <th>Fecha/Hora</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in studentStore.data_report" :key="index">
              <th scope="row">{{ ++index }}</th>
              <td>
                {{ `${item.student.last_name_father} ${item.student.last_name_mother}, ${item.student.name}` }}
              </td>
              <td>{{ `${item.created_at}` }}</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

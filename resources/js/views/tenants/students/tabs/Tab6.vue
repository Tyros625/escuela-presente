<script setup>
import { useStudentStore } from "@/stores/student";

const studentStore = useStudentStore();
</script>

<template>
  <h5>Seleccione las Caracteristicas</h5>
  <div class="row">
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.orthopedic_device"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Usa algún aparato ortopedico</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.complete_vaccinations"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Tiene todas las Vacunas</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.tonsils"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>se enferma de las amigdalas</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.medicines"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Toma algun medicamento</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.flatfoot"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Tiene pie Plano</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.glasses"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Usa Lentes</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.seizures"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>Sufre convulciones (ataques)</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.accidents"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>¿Ha sufrido Accidentes?</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.operations"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>¿Lo han operado?</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.medical_care.hearing_problems"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label>¿Tiene problemas auditivos?</label>
      </div>
    </div>
  </div>
</template>

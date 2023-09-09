<script setup>
import { useStudentStore } from "@/stores/student";
import options from "@/data/studentOptions";

const studentStore = useStudentStore();
</script>

<template>
  <h5>Datos de Salud e Higiene</h5>
  <div class="row">
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.healths.current_general_status"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.healths.current_general_status,
          }"
          v-uppercase
        />
        <label class="form-label"
          >Estado General de Salud Actual del Alumno</label
        >
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid': !studentStore.form.healths.blood_type,
          }"
          v-model="studentStore.form.healths.blood_type"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.blood_type">
            {{ item }}
          </option>
        </select>
        <label class="form-label">Tipo de Sangre</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.healths.chronic_disease"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.healths.chronic_disease,
          }"
          v-uppercase
        />
        <label class="form-label">Enfermedad Crónica</label>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.healths.has_medical_service"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label class="form-label">¿Tiene Servicio Médico?</label>
      </div>
    </div>
    <div class="col-md-6" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.healths.medical_service_number"
          class="form-control"
          v-uppercase
        />
        <label class="form-label">Número de Afiliación</label>
      </div>
    </div>
    <div class="col-sm-3" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.healths.medical_service_name"
          class="form-check-input"
          type="radio"
          id="example-radios-inline6"
          value="IMSS"
        />
        <label class="form-check-label" for="example-radios-inline6"
          >IMSS</label
        >
      </div>
    </div>
    <div class="col-sm-3" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.healths.medical_service_name"
          class="form-check-input"
          type="radio"
          id="example-radios-inline7"
          value="ISSTE"
        />
        <label class="form-check-label" for="example-radios-inline7"
          >ISSTE</label
        >
      </div>
    </div>
    <div class="col-sm-3" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.healths.medical_service_name"
          class="form-check-input"
          type="radio"
          id="example-radios-inline8"
          value="ISSEMYM"
        />
        <label class="form-check-label" for="example-radios-inline8"
          >ISSEMYM</label
        >
      </div>
    </div>
    <div class="col-sm-3" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.healths.medical_service_name"
          class="form-check-input"
          type="radio"
          id="example-radios-inline9"
          value="PARTICULAR"
        />
        <label class="form-check-label" for="example-radios-inline9"
          >PARTICULAR</label
        >
      </div>
    </div>
    <div class="col-sm-3" v-if="studentStore.form.healths.has_medical_service">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.healths.medical_service_name"
          class="form-check-input"
          type="radio"
          id="example-radios-inline10"
          value="Otro"
        />
        <label class="form-check-label" for="example-radios-inline10"
          >Otro</label
        >
      </div>
    </div>
  </div>
  <h5>Algun Familiar es afecto a: (Seleccione al menos uno)</h5>
  <div class="row">
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.coffee"
          id="example-checkbox-inline31"
        />
        <label class="form-check-label" for="example-checkbox-inline31"
          >Café</label
        >
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.cigar"
          id="example-checkbox-inline32"
        />
        <label class="form-check-label" for="example-checkbox-inline32"
          >Cigarro</label
        >
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="
            studentStore.form.healths.familiar_affection.alcoholic_drinks
          "
          id="example-checkbox-inline33"
        />
        <label class="form-check-label" for="example-checkbox-inline33"
          >Bebidas Alcoholicas</label
        >
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.drugs"
          id="example-checkbox-inline34"
        />
        <label class="form-check-label" for="example-checkbox-inline34"
          >Drogas</label
        >
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.dope"
          id="example-checkbox-inline35"
        />
        <label class="form-check-label" for="example-checkbox-inline35"
          >Marihuana</label
        >
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.sleeping_pills"
          id="example-checkbox-inline36"
        />
        <label class="form-check-label" for="example-checkbox-inline36"
          >Pastillas para dormir</label
        >
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.healths.familiar_affection.nerve_pills"
          id="example-checkbox-inline37"
        />
        <label class="form-check-label" for="example-checkbox-inline37"
          >Pastillas para los nervios</label
        >
      </div>
    </div>
  </div>
</template>

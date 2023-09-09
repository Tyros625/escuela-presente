<script setup>
import { useStudentStore } from "@/stores/student";
import options from "@/data/studentOptions";

const studentStore = useStudentStore();
</script>

<template>
  <h5>Datos Familiares</h5>
  <div class="row">
    <div class="col-md-12">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.relatives.student_live_with"
          :disabled="studentStore.isLoading"
        >
          <option :value="'ambos'">Papá y Mamá</option>
          <option :value="'papa'">Sólo Papá o Tutor</option>
          <option :value="'mama'">Sólo Mamá o Tutora</option>
        </select>
        <label class="form-label">¿Con quién vive el alumno(a)?</label>
      </div>
    </div>
  </div>
  <div
    class="row"
    v-if="
      studentStore.form.relatives.student_live_with === 'ambos' ||
      studentStore.form.relatives.student_live_with === 'papa'
    "
  >
    <h5>Datos del Padre o Tutor</h5>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.father_data.name"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.father_data.name,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Nombres Completo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.relatives.father_data.relationship"
          :disabled="studentStore.isLoading"
          :class="{
            'is-invalid': !studentStore.form.relatives.father_data.relationship,
          }"
        >
          <option v-for="item in options.relationship">
            {{ item }}
          </option>
        </select>
        <label>Parentesco</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.father_data.occupation"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.father_data.occupation,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Ocupación</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.father_data.work_address"
          class="form-control"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Domicilio del Trabajo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="number"
          v-model="studentStore.form.relatives.father_data.work_phone"
          class="form-control"
          :disabled="studentStore.isLoading"
        />
        <label>Teléfono del Trabajo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="number"
          v-model="studentStore.form.relatives.father_data.phone_whatsapp"
          class="form-control"
          :disabled="studentStore.isLoading"
        />
        <label>Tel Móvil con Whatsapp</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="email"
          v-model="studentStore.form.relatives.father_data.email"
          class="form-control"
          :disabled="studentStore.isLoading"
        />
        <label>Correo Electrónico</label>
      </div>
    </div>
  </div>
  <div
    class="row"
    v-if="
      studentStore.form.relatives.student_live_with === 'ambos' ||
      studentStore.form.relatives.student_live_with === 'mama'
    "
  >
    <h5>Datos de la Madre o Tutora</h5>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.mother_data.name"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.mother_data.name,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Nombres Completo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid': !studentStore.form.relatives.mother_data.relationship,
          }"
          v-model="studentStore.form.relatives.mother_data.relationship"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.relationship">
            {{ item }}
          </option>
        </select>
        <label>Parentesco</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.mother_data.occupation"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.mother_data.occupation,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Ocupación</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.mother_data.work_address"
          class="form-control"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label>Domicilio del Trabajo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="number"
          v-model="studentStore.form.relatives.mother_data.work_phone"
          class="form-control"
          :disabled="studentStore.isLoading"
        />
        <label>Teléfono del Trabajo</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="number"
          v-model="studentStore.form.relatives.mother_data.phone_whatsapp"
          class="form-control"
          :disabled="studentStore.isLoading"
        />
        <label>Tel Móvil con Whatsapp</label>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-floating mb-4">
        <input
          type="email"
          v-model="studentStore.form.relatives.mother_data.email"
          class="form-control"
        />
        <label>Correo Electrónico</label>
      </div>
    </div>
  </div>
  <h5>Personas Autorizadas Para Recoger Al Alumno en el Plantel</h5>
  <div
    class="row"
    v-for="(item, index) in studentStore.form.relatives.authorized_persons"
  >
    <div class="col-sm-6">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.authorized_persons[index].name"
          class="form-control"
          :class="{
            'is-invalid':
              !studentStore.form.relatives.authorized_persons[index].name,
          }"
          v-uppercase
        />
        <label>Persona {{ index + 1 }} - Nombre(s) Completo(s)</label>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid':
              !studentStore.form.relatives.authorized_persons[index]
                .relationship,
          }"
          v-model="
            studentStore.form.relatives.authorized_persons[index].relationship
          "
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.relationship">
            {{ item }}
          </option>
        </select>
        <label>Parentesco</label>
      </div>
    </div>
  </div>
  <h5>Hermanos o Familiares que Vivan en el Mismo Domicilio del Alumno</h5>
  <div
    class="row"
    v-for="(item, index) in studentStore.form.relatives.roommates"
  >
    <div class="col-sm-3">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.roommates[index].name"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.roommates[index].name,
          }"
          v-uppercase
        />
        <label>Nombre(s) Completo</label>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-floating mb-4">
        <input
          type="number"
          v-model="studentStore.form.relatives.roommates[index].age"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.relatives.roommates[index].age,
          }"
        />
        <label>Edad</label>
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid': !studentStore.form.relatives.roommates[index].sex,
          }"
          v-model="studentStore.form.relatives.roommates[index].sex"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.sex">
            {{ item }}
          </option>
        </select>
        <label>Sexo</label>
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid':
              !studentStore.form.relatives.roommates[index].scholarship,
          }"
          v-model="studentStore.form.relatives.roommates[index].scholarship"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.scholarship2">
            {{ item }}
          </option>
        </select>
        <label>Escolaridad</label>
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.relatives.roommates[index].occupation"
          class="form-control"
          :class="{
            'is-invalid':
              !studentStore.form.relatives.roommates[index].occupation,
          }"
          v-uppercase
        />
        <label>Ocupación</label>
      </div>
    </div>
    <div class="col-sm-2">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid':
              !studentStore.form.relatives.roommates[index].relationship,
          }"
          v-model="studentStore.form.relatives.roommates[index].relationship"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.relationship">
            {{ item }}
          </option>
        </select>
        <label>Parentesco</label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useStudentStore } from "@/stores/student";
import options from "@/data/studentOptions";

const studentStore = useStudentStore();
</script>

<template>
  <h5>Datos Socioeconómicos</h5>
  <div class="row">
    <div class="col-md-3">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          v-model="studentStore.form.socioeconomics.general.student_works"
          :disabled="studentStore.isLoading"
        >
          <option :value="true">SI</option>
          <option :value="false">NO</option>
        </select>
        <label class="form-label">¿El Alumno Trabaja?</label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.socioeconomics.general.what_works"
          class="form-control"
          :class="{
            'is-invalid': !studentStore.form.socioeconomics.general.what_works,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label class="form-label">¿En qué trabaja?</label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-floating mb-4">
        <input
          type="text"
          v-model="studentStore.form.socioeconomics.general.working_hours"
          class="form-control"
          :class="{
            'is-invalid':
              !studentStore.form.socioeconomics.general.working_hours,
          }"
          :disabled="studentStore.isLoading"
          v-uppercase
        />
        <label class="form-label">Horario de Trabajo</label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid':
              !studentStore.form.socioeconomics.general.family_economy,
          }"
          v-model="studentStore.form.socioeconomics.general.family_economy"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in options.family_economy">
            {{ item }}
          </option>
        </select>
        <label class="form-label">Economía Familiar</label>
      </div>
    </div>
  </div>
  <h5>El Alumno Cuenta con</h5>
  <div class="row">
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.socioeconomics.ownerships.kind_house"
          class="form-check-input"
          type="radio"
          id="example-radios-inline1"
          value="Casa Propia"
        />
        <label class="form-check-label" for="example-radios-inline1">
          Casa Propia
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.socioeconomics.ownerships.kind_house"
          class="form-check-input"
          type="radio"
          id="example-radios-inline2"
          value="Casa Rentada"
        />
        <label class="form-check-label" for="example-radios-inline2">
          Casa Rentada
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.socioeconomics.ownerships.kind_house"
          class="form-check-input"
          type="radio"
          id="example-radios-inline3"
          value="Casa Prestada"
        />
        <label class="form-check-label" for="example-radios-inline3">
          Casa Prestada
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.socioeconomics.ownerships.kind_house"
          class="form-check-input"
          type="radio"
          id="example-radios-inline4"
          value="Casa a Crédito"
        />
        <label class="form-check-label" for="example-radios-inline4">
          Casa a Crédito
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          v-model="studentStore.form.socioeconomics.ownerships.kind_house"
          class="form-check-input"
          type="radio"
          id="example-radios-inline5"
          value="Otro"
        />
        <label class="form-check-label" for="example-radios-inline5">
          Otro
        </label>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.recamara_propia"
          id="example-checkbox-inline1"
        />
        <label class="form-check-label" for="example-checkbox-inline1">
          Recamara Propia
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.refrigerador"
          id="example-checkbox-inline2"
        />
        <label class="form-check-label" for="example-checkbox-inline2">
          Refrigerador
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.lavadora"
          id="example-checkbox-inline3"
        />
        <label class="form-check-label" for="example-checkbox-inline3">
          Lavadora
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.estereo"
          id="example-checkbox-inline4"
        />
        <label class="form-check-label" for="example-checkbox-inline4">
          Estereo
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.automovil"
          id="example-checkbox-inline5"
        />
        <label class="form-check-label" for="example-checkbox-inline5">
          Automóvil
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.computadora"
          id="example-checkbox-inline6"
        />
        <label class="form-check-label" for="example-checkbox-inline6">
          Computadora
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.internet"
          id="example-checkbox-inline7"
        />
        <label class="form-check-label" for="example-checkbox-inline7">
          Internet
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.estufa"
          id="example-checkbox-inline8"
        />
        <label class="form-check-label" for="example-checkbox-inline8">
          Estufa
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.microondas"
          id="example-checkbox-inline9"
        />
        <label class="form-check-label" for="example-checkbox-inline9">
          Microondas
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.baño_propio"
          id="example-checkbox-inline10"
        />
        <label class="form-check-label" for="example-checkbox-inline10">
          Baño Propio
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.agua_drenaje"
          id="example-checkbox-inline11"
        />
        <label class="form-check-label" for="example-checkbox-inline11">
          Agua y Drenaje
        </label>
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.ownerships.tv_paga"
          id="example-checkbox-inline12"
        />
        <label class="form-check-label" for="example-checkbox-inline12">
          TV de Paga
        </label>
      </div>
    </div>
  </div>
  <h5>
    Alimentos que Consume Regularmente el Alumno: (Seleccione al menos uno)
  </h5>
  <h6 class="fw-normal">Desayuno</h6>
  <div class="row">
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.breakfast.milk"
          id="example-checkbox-inline13"
        />
        <label class="form-check-label" for="example-checkbox-inline13">
          Leche
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.breakfast.egg"
          id="example-checkbox-inline14"
        />
        <label class="form-check-label" for="example-checkbox-inline14">
          Huevo
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.breakfast.fruit"
          id="example-checkbox-inline15"
        />
        <label class="form-check-label" for="example-checkbox-inline15">
          Fruta
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.breakfast.cereal"
          id="example-checkbox-inline16"
        />
        <label class="form-check-label" for="example-checkbox-inline16">
          Cereal
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.breakfast.bread"
          id="example-checkbox-inline17"
        />
        <label class="form-check-label" for="example-checkbox-inline17">
          Pan
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="
            studentStore.form.socioeconomics.nutrition.breakfast.fast_food
          "
          id="example-checkbox-inline18"
        />
        <label class="form-check-label" for="example-checkbox-inline18">
          Comida Chatarra
        </label>
      </div>
    </div>
  </div>
  <h6 class="fw-normal">Almuerzo</h6>
  <div class="row">
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.meat"
          id="example-checkbox-inline19"
        />
        <label class="form-check-label" for="example-checkbox-inline19">
          Carnes
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.fish"
          id="example-checkbox-inline20"
        />
        <label class="form-check-label" for="example-checkbox-inline20">
          Pescado
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.vegetable"
          id="example-checkbox-inline21"
        />
        <label class="form-check-label" for="example-checkbox-inline21">
          Verdura
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.flour"
          id="example-checkbox-inline22"
        />
        <label class="form-check-label" for="example-checkbox-inline22">
          Harinas
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.fruit"
          id="example-checkbox-inline23"
        />
        <label class="form-check-label" for="example-checkbox-inline23">
          Frutas
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.lunch.fast_food"
          id="example-checkbox-inline24"
        />
        <label class="form-check-label" for="example-checkbox-inline24">
          Comida Chatarra
        </label>
      </div>
    </div>
  </div>
  <h6 class="fw-normal">Cena</h6>
  <div class="row">
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.milk"
          id="example-checkbox-inline25"
        />
        <label class="form-check-label" for="example-checkbox-inline25">
          Leche
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.coffee"
          id="example-checkbox-inline26"
        />
        <label class="form-check-label" for="example-checkbox-inline26">
          Café
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.meat"
          id="example-checkbox-inline27"
        />
        <label class="form-check-label" for="example-checkbox-inline27">
          Carnes
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.egg"
          id="example-checkbox-inline28"
        />
        <label class="form-check-label" for="example-checkbox-inline28">
          Huevo
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.flour"
          id="example-checkbox-inline29"
        />
        <label class="form-check-label" for="example-checkbox-inline29">
          Harinas
        </label>
      </div>
    </div>
    <div class="col-sm-4 mt-2">
      <div class="form-check form-check-inline mb-4">
        <input
          class="form-check-input"
          type="checkbox"
          v-model="studentStore.form.socioeconomics.nutrition.dinner.fast_food"
          id="example-checkbox-inline30"
        />
        <label class="form-check-label" for="example-checkbox-inline30">
          Comida Chatarra
        </label>
      </div>
    </div>
  </div>
</template>

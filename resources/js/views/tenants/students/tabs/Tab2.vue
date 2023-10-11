<script setup>
import { useStudentStore } from "@/stores/student";
import options from "@/data/studentOptions";

const studentStore = useStudentStore();
</script>

<template>
	<div class="row">
		<h5>Datos Académicos</h5>
		<div class="col-md-6">
			<div
				class="form-floating mb-4"
				v-if="
					studentStore.paymentType === 'REPOSICIÓN DE CREDENCIAL' ||
					studentStore.paymentType === 'REINSCRIPCIÓN'
				"
			>
				<input
					type="text"
					v-model="studentStore.form.grade"
					class="form-control"
					readonly
				/>
				<label class="form-label">Grado</label>
			</div>

			<div class="form-floating mb-4">
				<select
					class="form-select"
					:class="{
						'is-invalid': !studentStore.form.academic_group_id,
					}"
					v-model="studentStore.form.academic_group_id"
					:disabled="studentStore.isLoading"
				>
					<option v-for="item in studentStore.groups" :value="item.id">
						{{ `${item.grade}-${item.section}` }}
					</option>
				</select>
				<label class="form-label">Grado/Grupo</label>
			</div>
		</div>
		<!-- <div class="col-md-12">
      <div class="form-floating mb-4">
        <select
          class="form-select"
          :class="{
            'is-invalid': !studentStore.form.academic_group_id,
          }"
          v-model="studentStore.form.academic_group_id"
          :disabled="studentStore.isLoading"
        >
          <option v-for="item in studentStore.groups" :value="item.id">
            {{ `${item.grade} - ${item.section} | ${item.school_year}` }}
          </option>
        </select>
        <label class="form-label">Grupo Académico</label>
      </div>
    </div> -->
		<div class="col-md-6">
			<div class="form-floating mb-4">
				<input
					type="text"
					v-model="studentStore.form.academic.udeei"
					class="form-control"
					:disabled="studentStore.isLoading"
					v-uppercase
				/>
				<label class="form-label">Educación Especial UDEEI</label>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-floating mb-4">
				<input
					type="text"
					v-model="studentStore.form.academic.origin_school"
					class="form-control"
					:class="{
						'is-invalid': !studentStore.form.academic.origin_school,
					}"
					:disabled="studentStore.isLoading"
					v-uppercase
				/>
				<label class="form-label">Escuela de Procedencia</label>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					:class="{
						'is-invalid': !studentStore.form.academic.federal_entity_school,
					}"
					v-model="studentStore.form.academic.federal_entity_school"
					:disabled="studentStore.isLoading"
				>
					<option v-for="item in options.federal_entity_school">
						{{ item }}
					</option>
				</select>
				<label class="form-label">Entidad Federativa de la Escuela</label>
			</div>
		</div>
	</div>
</template>

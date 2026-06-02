<script setup>
import { useStudentStore } from "@/stores/student";
import options from "@/data/studentOptions";

const studentStore = useStudentStore();
const file = ref();
const route = useRoute();

function onChangeFile(e) {
	file.value = e.target.files[0];
	uploadFile();
	studentStore.isLoading = true;
}

async function uploadFile() {
	const formData = new FormData();
	formData.append("file", file.value);

	return await api.post(`upload-file`, formData).then((response) => {
		studentStore.form.photo = response.data;
		studentStore.isLoading = false;
	});
}
</script>

<template>
	<div class="row">
		<h5>Datos Personales</h5>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					class="form-control"
					name="name"
					:class="{
						'is-invalid': !studentStore.form.name,
					}"
					v-model="studentStore.form.name"
					:disabled="studentStore.isLoading"
					v-uppercase
				/>
				<label>Nombres</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					class="form-control"
					name="lastname"
					:class="{
						'is-invalid': !studentStore.form.last_name_father,
					}"
					v-model="studentStore.form.last_name_father"
					:disabled="studentStore.isLoading"
					v-uppercase
				/>
				<label>Apellido Paterno</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					class="form-control"
					name="lastname"
					v-model="studentStore.form.last_name_mother"
					:disabled="studentStore.isLoading"
					v-uppercase
					:class="{
						'is-invalid': !studentStore.form.last_name_mother,
					}"
				/>
				<label>Apellido Materno</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.nationality"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.nationality,
					}"
				>
					<option v-for="item in options.nationality">
						{{ item }}
					</option>
				</select>
				<label>Nacionalidad</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					v-if="
						studentStore.paymentType === 'NUEVO INGRESO' ||
						route.name === 'students.add' ||
						route.name === 'students.edit'
					"
					type="text"
					v-model="studentStore.form.curp"
					:class="{
						'is-invalid': !studentStore.form.curp,
					}"
					class="form-control"
					v-uppercase
				/>
				<input
					v-else
					type="text"
					v-model="studentStore.form.curp"
					class="form-control"
					readonly
					v-uppercase
				/>
				<label>CURP</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="mb-4 ep-date-field">
				<label class="form-label">Fecha de Nacimiento</label>
				<div
					class="ep-date-field__input"
					:class="{ 'ep-date-field__input--empty': !studentStore.form.date_birth }"
				>
					<input
						type="date"
						v-model="studentStore.form.date_birth"
						class="form-control ep-date-input"
						:class="{ 'is-invalid': !studentStore.form.date_birth }"
						:disabled="studentStore.isLoading"
					/>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.place_birth"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.place_birth,
					}"
				>
					<option v-for="item in options.place_birth">
						{{ item }}
					</option>
				</select>
				<label>Lugar de Nacimiento</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.sex"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.sex,
					}"
				>
					<option v-for="item in options.sex">
						{{ item }}
					</option>
				</select>
				<label>Sexo</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="number"
					v-model="studentStore.form.weight"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.weight,
					}"
				/>
				<label>Peso (kg)</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="number"
					step="0.01"
					v-model="studentStore.form.height"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.height,
					}"
				/>
				<label>Estatura (cm)</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.is_migrant"
					:disabled="studentStore.isLoading"
				>
					<option :value="true">SI</option>
					<option :value="false">NO</option>
				</select>
				<label>Migrante</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.indigenous_group"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.indigenous_group,
					}"
				>
					<option v-for="item in options.indigenous_group">
						{{ item }}
					</option>
				</select>
				<label>Grupo Indígena</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.indigenous_language"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.indigenous_language,
					}"
				>
					<option v-for="item in options.indigenous_language">
						{{ item }}
					</option>
				</select>
				<label>Lengua Indígena</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.disability"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.disability,
					}"
				>
					<option v-for="item in options.disability">
						{{ item }}
					</option>
				</select>
				<label>Discapacidad</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.health_insurance"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.health_insurance,
					}"
				>
					<option v-for="item in options.health_insurance">
						{{ item }}
					</option>
				</select>
				<label>Seguro Medico</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.scholarship"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.scholarship,
					}"
				>
					<option v-for="item in options.scholarship">
						{{ item }}
					</option>
				</select>
				<label>BECA</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					v-model="studentStore.form.address"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.address,
					}"
					v-uppercase
				/>
				<label>Domicilio, Calle y Número</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					v-model="studentStore.form.colony"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.colony,
					}"
					v-uppercase
				/>
				<label>Colonia</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="number"
					v-model="studentStore.form.postal_code"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.postal_code,
					}"
					v-uppercase
				/>
				<label>Código Postal</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="text"
					v-model="studentStore.form.municipality"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.municipality,
					}"
					v-uppercase
				/>
				<label>Delegación o Municipio</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<select
					class="form-select"
					v-model="studentStore.form.federal_entity"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.federal_entity,
					}"
				>
					<option v-for="item in options.federal_entity">
						{{ item }}
					</option>
				</select>
				<label>Entidad Federativa</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="number"
					class="form-control"
					v-model="studentStore.form.home_phone"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.home_phone,
					}"
				/>
				<label>Teléfono de Casa</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4">
				<input
					type="email"
					v-model="studentStore.form.email"
					class="form-control"
					:disabled="studentStore.isLoading"
					:class="{
						'is-invalid': !studentStore.form.email,
					}"
				/>
				<label>Correo Electrónico</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-floating mb-4 text-danger fw-bold">
				Colocar un correo Vigente y en uso, le llegará información importante.
			</div>
		</div>
		<div class="col-md-6 mb-4">
			<label class="form-label">Fotografía</label>
			<div class="ep-file-upload">
				<div class="ep-file-upload__control">
					<input
						class="form-control"
						type="file"
						accept="image/*"
						@change="onChangeFile"
					/>
					<div class="form-text">JPG o PNG, máximo 2 MB recomendado.</div>
				</div>
				<LoaderView v-if="studentStore.isLoading" />
				<img
					v-else-if="studentStore.form.photo"
					:src="studentStore.form.photo"
					class="ep-photo-preview"
					alt="Fotografía del estudiante"
					loading="lazy"
				/>
				<div v-else class="ep-photo-placeholder" aria-hidden="true">
					<i class="fa-regular fa-user"></i>
				</div>
			</div>
		</div>
	</div>
</template>

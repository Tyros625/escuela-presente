<template>
	<!-- <BasePageHeading title="Estudiantes" /> -->
	<BasePageHeading title="Estudiantes">
		<template #extra>
			<button
				type="button"
				class="btn btn-outline-success btn-sm"
				data-bs-toggle="modal"
				data-bs-target="#exampleModal"
			>
				<i class="fa-solid fa-file-excel me-1"></i>
				Importar
			</button>
			<router-link :to="{ name: `students.add` }" class="btn btn-primary btn-sm">
				<i class="fa fa-plus me-1"></i>
				Agregar estudiante
			</router-link>
		</template>
	</BasePageHeading>

	<div class="content">
		<BaseBlock title="Filtros de Búsqueda">
			<form @submit.prevent="submit" class="mb-4">
				<div class="row">
					<div class="col-md-3">
						<label class="form-label">Matrícula</label>
						<input
							class="form-control"
							type="text"
							v-model="form.enrollment"
							v-uppercase
						/>
					</div>
					<div class="col-md-3">
						<label class="form-label">Nombre</label>
						<input class="form-control" type="text" v-model="form.name" v-uppercase />
					</div>
					<div class="col-md-3">
						<label class="form-label">Grado</label>
						<select class="form-select" v-model="form.grade">
							<option :value="null">- Ninguno -</option>
							<option v-for="item in grades" :value="item.description">
								{{ item.description }}
							</option>
						</select>
					</div>
					<div class="col-md-3">
						<label class="form-label">Grupo</label>
						<select class="form-select" v-model="form.group">
							<option :value="null">- Ninguno -</option>
							<option v-for="item in sections" :value="item.description">
								{{ item.description }}
							</option>
						</select>
					</div>

					<div class="col-md-12 mt-3">
						<button type="submit" class="btn btn-primary btn-sm" :disabled="isLoading">
							<i class="fa fa-cog fa-spin me-1" v-if="isLoading"></i>
							<i class="fa-solid fa-magnifying-glass me-1" v-else></i>
							Consultar
						</button>
					</div>

					<div class="col-md-12 mt-3">
						<div class="table-responsive">
							<table class="table">
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
									<tr v-for="(item, index) in records" :key="index">
										<th scope="row">{{ ++index }}</th>
										<td>{{ item.enrollment }}</td>
										<td>
											{{
												`${item.last_name_father} ${item.last_name_mother}, ${item.name}`
											}}
										</td>
										<td>{{ `${item.age} años` }}</td>
										<td>{{ `${item.grade}-${item.group}` }}</td>
										<td>
											<div class="dropdown">
												<button
													class="btn btn-sm btn-secondary dropdown-toggle"
													type="button"
													data-bs-toggle="dropdown"
													aria-expanded="false"
												>
													Acciones
												</button>
												<ul class="dropdown-menu">
													<router-link
														:to="{
															name: `students.detail`,
															params: { id: item.enrollment },
														}"
													>
														<li><a class="dropdown-item" href="#">Ficha</a></li>
													</router-link>
													<router-link
														:to="{
															name: `students.edit`,
															params: { id: item.enrollment },
														}"
													>
														<li><a class="dropdown-item" href="#">Editar</a></li>
													</router-link>
													<router-link
														:to="{
															name: `students.incident`,
															params: { id: item.enrollment },
														}"
													>
														<li><a class="dropdown-item" href="#">Incidencias</a></li>
													</router-link>
													<div @click="deleteStudent(item.enrollment)">
														<li><a class="dropdown-item">Eliminar</a></li>
													</div>
												</ul>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</form>
		</BaseBlock>
	</div>

	<!-- Modal -->
	<div
		class="modal fade"
		id="exampleModal"
		tabindex="-1"
		aria-labelledby="exampleModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Importar Estudiantes</h1>
					<button
						type="button"
						class="btn-close"
						data-bs-dismiss="modal"
						aria-label="Close"
					></button>
				</div>
				<div class="modal-body">
					<ErrorsView v-if="errors.length" :errors="errors" />
					<form @submit.prevent="importXLS">
						<div class="mb-3">
							<label for="formFile" class="form-label">Seleccione archivo</label>
							<input
								class="form-control"
								type="file"
								id="formFile"
								@change="onChangeFile"
							/>
						</div>
						<div class="mb-3 text-center">
							<a href="/imports/alumnos.xlsx">Descargar Ejemplo</a>
						</div>
						<div class="col-md-12 mt-3 text-end">
							<button type="submit" class="btn btn-primary" :disabled="isLoading">
								<i class="fa fa-cog fa-spin" v-if="isLoading"></i>
								<i class="fa-solid fa-floppy-disk" v-else></i>
								Importar
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import Swal from "sweetalert2";

const initialForm = {
	enrollment: null,
	name: null,
	grade: null,
	group: null,
};
const form = reactive({ ...initialForm });
const records = ref([]);
const isLoading = ref(false);
const errors = ref([]);
const file = ref();
const grades = ref([]);
const sections = ref([]);

onMounted(async () => {
	await getGrades();
	await getSections();
});

const getGrades = async () => {
	const { data } = await api.get(`grades`);
	grades.value = data.data;
};

const getSections = async () => {
	const { data } = await api.get(`sections`);
	sections.value = data.data;
};

function onChangeFile(e) {
	file.value = e.target.files[0];
}

function importXLS() {
	const formData = new FormData();
	formData.append("file", file.value);

	api
		.post(`/students/import`, formData)
		.then(async function (res) {
			Toast.fire({
				icon: "success",
				title: res.data.message,
			});
			modalHide(`modal-import`);
			await getData();
		})
		.catch(function (err) {
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

			isLoading.value = false;
		});
}

function submit() {
	isLoading.value = true;

	api
		.get(`students`, { params: form })
		.then((res) => {
			Toast.fire({
				icon: "success",
				title: "Consulta Correcta",
			});
			records.value = res.data.data;
			isLoading.value = false;
		})
		.catch((err) => {
			console.log(err.response);
			isLoading.value = false;
		});
}

function deleteStudent(enrollment) {
	Swal.fire({
		title: "¿Estás segurx de eliminar?",
		showDenyButton: false,
		showCancelButton: true,
		confirmButtonText: "Si",
		cancelButtonText: "Cancelar",
	}).then((result) => {
		if (result.isConfirmed) {
			api
				.delete(`/students/${enrollment}`)
				.then((res) => {
					if (res.status === 200) {
						Toast.fire({
							icon: "success",
							title: "Eliminado correctamente",
						});
						submit();
					}
				})
				.catch((error) => {
					Toast.fire({
						icon: "error",
						title: error.message,
					});
				});
		}
	});
}
</script>

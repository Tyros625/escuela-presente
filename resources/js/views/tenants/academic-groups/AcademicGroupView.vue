<template>
	<BasePageHeading
		title="Gestión de Materias"
		:subtitle="selectedCycleName ? `— Ciclo Escolar ${selectedCycleName}` : ''"
	>
		<template #extra>
			<div class="d-flex gap-2 align-items-center flex-wrap">
				<select class="form-select form-select-sm w-auto" v-model="selectedSchoolCycleId">
					<option :value="null">Todos los ciclos</option>
					<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
						{{ c.description }}
					</option>
				</select>

				<button type="button" class="btn btn-sm btn-alt-primary btn-icon-center" disabled>
					<i class="fa-solid fa-wand-magic-sparkles"></i>
					Asignación Auto
				</button>

				<button type="button" class="btn btn-sm btn-primary btn-icon-center" @click="openCreateModal">
					<i class="fa fa-plus opacity-50"></i>
					Nueva Materia
				</button>
			</div>
		</template>
	</BasePageHeading>

	<div class="content">
		<BaseBlock content-full>
			<template #header>
				<div class="d-flex align-items-center justify-content-between w-100 py-1 gap-3 flex-wrap">
					<div class="d-flex align-items-center gap-2">
						<i class="fa-solid fa-book text-primary"></i>
						<h3 class="block-title mb-0">Catálogo de Materias</h3>
					</div>
					<div class="d-flex align-items-center gap-2 flex-wrap">
						<div class="search-wrap">
							<i class="fa-solid fa-magnifying-glass search-icon"></i>
							<input
								type="text"
								class="form-control form-control-sm search-input"
								v-model.trim="searchText"
								placeholder="Buscar materia..."
							/>
						</div>
						<select class="form-select form-select-sm w-auto" v-model="selectedGradeId">
							<option :value="null">Todos los grados</option>
							<option v-for="g in grades" :key="g.id" :value="g.id">
								{{ g.description }}
							</option>
						</select>
					</div>
				</div>
			</template>

			<div class="table-responsive">
				<table class="table table-striped table-hover mb-0 align-middle">
					<thead>
						<tr class="text-uppercase fs-xs">
							<th>Clave</th>
							<th>Materia</th>
							<th>Grado</th>
							<th class="text-center">Horas/Sem.</th>
							<th class="text-center">Créditos</th>
							<th>Campo Formativo</th>
							<th class="text-end">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in filteredRows" :key="row.id">
							<td class="fw-semibold">{{ row.code }}</td>
							<td>{{ row.subjectName }}</td>
							<td>
								<span class="badge bg-body-secondary text-body-emphasis">{{ row.gradeLabel }}</span>
							</td>
							<td class="text-center fw-semibold">{{ row.hoursPerWeek }}h</td>
							<td class="text-center">{{ row.credits }}</td>
							<td>
								<span class="badge bg-info-subtle text-info-emphasis">
									{{ row.trainingField }}
								</span>
							</td>
							<td class="text-end">
								<div class="btn-group">
									<button
										type="button"
										class="btn btn-sm btn-alt-warning"
										@click="openEditModal(row.source)"
										title="Editar"
									>
										<i class="fa-solid fa-pen"></i>
									</button>
									<button
										type="button"
										class="btn btn-sm btn-alt-danger"
										@click="confirmDelete(row.source)"
										title="Eliminar"
									>
										<i class="fa-solid fa-trash"></i>
									</button>
								</div>
							</td>
						</tr>
						<tr v-if="!filteredRows.length">
							<td colspan="7" class="text-center text-muted py-5">
								<i class="fa-solid fa-folder-open fa-2x d-block mb-2 opacity-50"></i>
								Sin materias registradas
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</BaseBlock>
	</div>

	<div
		class="modal fade"
		id="modal-academic-groups"
		tabindex="-1"
		aria-labelledby="modal-academic-groups-label"
		aria-hidden="true"
		data-bs-backdrop="static"
	>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title d-flex align-items-center gap-2" id="modal-academic-groups-label">
						<i class="fa-solid fa-book-open text-primary"></i>
						{{ modalType === 'add' ? 'Nueva Materia' : 'Editar Materia' }}
					</h5>
					<button
						type="button"
						class="btn-close"
						data-bs-dismiss="modal"
						aria-label="Close"
					></button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="onSubmit">
						<div class="row g-3">
							<div class="col-md-6">
								<label class="form-label fw-semibold">Clave</label>
								<input
									class="form-control"
									type="text"
									v-model.trim="form.code"
									placeholder="Ej. MAT1"
									v-uppercase
									:disabled="isSaving"
									required
								/>
							</div>
							<div class="col-md-6">
								<label class="form-label fw-semibold">Nombre de la Materia</label>
								<input
									class="form-control"
									type="text"
									v-model.trim="form.subject_name"
									placeholder="Ej. Matemáticas I"
									:disabled="isSaving"
									required
								/>
							</div>
							<div class="col-md-6">
								<label class="form-label fw-semibold">Grado</label>
								<select class="form-select" v-model="form.grade_id" :disabled="isSaving" required>
									<option value="">Selecciona un grado...</option>
									<option v-for="g in grades" :key="g.id" :value="g.id">
										{{ g.description }}
									</option>
								</select>
							</div>
							<div class="col-md-3">
								<label class="form-label fw-semibold">Horas por Semana</label>
								<input
									class="form-control"
									type="number"
									min="1"
									max="40"
									step="1"
									v-model.number="form.hours_per_week"
									:disabled="isSaving"
									required
								/>
							</div>
							<div class="col-md-3">
								<label class="form-label fw-semibold">Créditos</label>
								<input
									class="form-control"
									type="number"
									min="1"
									max="20"
									step="1"
									v-model.number="form.credits"
									:disabled="isSaving"
									required
								/>
							</div>
							<div class="col-md-6">
								<label class="form-label fw-semibold">Año Escolar</label>
								<select class="form-select" v-model="form.school_cycle_id" :disabled="isSaving" required>
									<option value="">Selecciona un ciclo...</option>
									<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
										{{ c.description }}
									</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="form-label fw-semibold">Campo Formativo</label>
								<select class="form-select" v-model="form.training_field" :disabled="isSaving" required>
									<option value="">Selecciona un campo...</option>
									<option v-for="field in trainingFieldOptions" :key="field" :value="field">
										{{ field }}
									</option>
								</select>
							</div>
							<div class="col-12 d-flex justify-content-end gap-2 mt-2">
								<button
									type="button"
									class="btn btn-alt-secondary"
									data-bs-dismiss="modal"
									:disabled="isSaving"
								>
									Cancelar
								</button>
								<button
									type="submit"
									class="btn btn-primary btn-icon-center"
									:disabled="isSaving"
								>
									<i class="fa fa-cog fa-spin" v-if="isSaving"></i>
									<i class="fa-solid fa-floppy-disk" v-else></i>
									{{ modalType === 'add' ? 'Guardar Materia' : 'Actualizar Materia' }}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import api from '@/services/api';
import Swal from 'sweetalert2';

const trainingFieldOptions = [
	'Lenguajes',
	'Saberes y Pensamiento Científico',
	'Ética, Naturaleza y Sociedades',
	'De lo Humano y lo Comunitario',
];

const groups = ref([]);
const grades = ref([]);
const schoolCycles = ref([]);
const selectedSchoolCycleId = ref(null);
const selectedGradeId = ref(null);
const searchText = ref('');

const modalType = ref('add');
const rowSelected = ref(null);
const isSaving = ref(false);

const form = reactive({
	code: '',
	subject_name: '',
	grade_id: '',
	school_cycle_id: '',
	hours_per_week: 5,
	credits: 8,
	training_field: trainingFieldOptions[0],
});

const selectedCycleName = computed(() => {
	if (!selectedSchoolCycleId.value) return '';
	const c = schoolCycles.value.find((cycle) => cycle.id === selectedSchoolCycleId.value);
	return c ? c.description : '';
});

function normalizeSubjectRow(g) {
	const subjects = Array.isArray(g.subjects) ? g.subjects.filter(Boolean) : [];
	const trainingField = subjects.find((item) => trainingFieldOptions.includes(item)) || trainingFieldOptions[0];
	const credits = Math.max(1, Number(g.subjects_count || subjects.length || 1));

	return {
		id: g.id,
		code: g.name || g.group_label || '-',
		subjectName: g.room_name || g.group_label || '-',
		gradeId: g.grade_id,
		gradeLabel: g.grade || '-',
		hoursPerWeek: Math.max(1, Number(g.student_limit || 1)),
		credits,
		trainingField,
		source: g,
	};
}

const filteredRows = computed(() => {
	const search = searchText.value.toLowerCase();
	return groups.value
		.map(normalizeSubjectRow)
		.filter((row) => {
			if (selectedSchoolCycleId.value && row.source.school_cycle_id !== selectedSchoolCycleId.value) {
				return false;
			}
			if (selectedGradeId.value && row.gradeId !== selectedGradeId.value) {
				return false;
			}
			if (!search) return true;
			return (
				row.code.toLowerCase().includes(search) ||
				row.subjectName.toLowerCase().includes(search) ||
				row.gradeLabel.toLowerCase().includes(search) ||
				row.trainingField.toLowerCase().includes(search)
			);
		});
});

async function fetchLists() {
	const [g, c] = await Promise.all([api.get('/lists/grades'), api.get('/lists/school-cycles')]);
	grades.value = g.data;
	schoolCycles.value = c.data;
}

async function fetchGroups() {
	const { data } = await api.get('/academic-groups');
	groups.value = data.data;
}

function resetForm() {
	Object.assign(form, {
		code: '',
		subject_name: '',
		grade_id: '',
		school_cycle_id: selectedSchoolCycleId.value || '',
		hours_per_week: 5,
		credits: 8,
		training_field: trainingFieldOptions[0],
	});
}

function openCreateModal() {
	modalType.value = 'add';
	rowSelected.value = null;
	resetForm();
	showModal();
}

function openEditModal(g) {
	const row = normalizeSubjectRow(g);
	modalType.value = 'edit';
	rowSelected.value = g;
	Object.assign(form, {
		code: row.code,
		subject_name: row.subjectName,
		grade_id: g.grade_id,
		school_cycle_id: g.school_cycle_id,
		hours_per_week: row.hoursPerWeek,
		credits: row.credits,
		training_field: row.trainingField,
	});
	showModal();
}

function showModal() {
	const el = document.getElementById('modal-academic-groups');
	const m = new bootstrap.Modal(el);
	m.show();
}

function hideModal() {
	const el = document.getElementById('modal-academic-groups');
	const m = bootstrap.Modal.getOrCreateInstance(el);
	m.hide();
}

async function onSubmit() {
	isSaving.value = true;
	try {
		const credits = Math.max(1, Math.min(20, Number(form.credits || 1)));
		const subjectsArray = Array.from({ length: credits }, () => form.training_field);

		const payload = {
			name: form.code,
			grade_id: form.grade_id,
			section_id: null,
			school_cycle_id: form.school_cycle_id,
			shift: 'morning',
			room_name: form.subject_name,
			student_limit: Math.max(1, Number(form.hours_per_week || 1)),
			subjects: subjectsArray,
		};

		if (modalType.value === 'add') {
			await api.post('/academic-groups', payload);
		} else {
			await api.put(`/academic-groups/${rowSelected.value.id}`, payload);
		}

		await fetchGroups();
		hideModal();

		Swal.fire({
			icon: 'success',
			title: modalType.value === 'add' ? 'Materia creada' : 'Materia actualizada',
			text: form.subject_name,
			timer: 1500,
			showConfirmButton: false,
		});
	} catch (err) {
		const msg =
			err?.response?.data?.message ||
			Object.values(err?.response?.data?.errors || {}).flat().join('\n') ||
			'Error al guardar';
		Swal.fire({ icon: 'error', title: 'Error', text: msg });
	} finally {
		isSaving.value = false;
	}
}

function confirmDelete(g) {
	const row = normalizeSubjectRow(g);
	Swal.fire({
		title: '¿Eliminar materia?',
		text: `${row.code} - ${row.subjectName}`,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Sí, eliminar',
		cancelButtonText: 'Cancelar',
		confirmButtonColor: '#dc3545',
	}).then(async (result) => {
		if (!result.isConfirmed) return;
		try {
			await api.delete(`/academic-groups/${g.id}`);
			await fetchGroups();
		} catch {
			Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar la materia.' });
		}
	});
}

watch(selectedSchoolCycleId, (val) => {
	form.school_cycle_id = val || '';
});

onMounted(async () => {
	await fetchLists();
	await fetchGroups();
	if (schoolCycles.value.length && selectedSchoolCycleId.value == null) {
		selectedSchoolCycleId.value = schoolCycles.value[0].id;
	}
	form.school_cycle_id = selectedSchoolCycleId.value || '';
});
</script>

<style scoped>
.btn-icon-center {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.35rem;
}

.btn-icon-center i {
	line-height: 1;
}

.search-wrap {
	position: relative;
	min-width: 220px;
}

.search-icon {
	position: absolute;
	left: 0.65rem;
	top: 50%;
	transform: translateY(-50%);
	opacity: 0.55;
	pointer-events: none;
}

.search-input {
	padding-left: 2rem;
}
</style>

<template>
	<BasePageHeading
		title="Gestión de Grupos"
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
					Asignación
				</button>

				<button type="button" class="btn btn-sm btn-primary btn-icon-center" @click="openCreateModal">
					<i class="fa fa-plus opacity-50"></i>
					Nuevo Grupo
				</button>
			</div>
		</template>
	</BasePageHeading>

	<div class="content">
		<BaseBlock content-full>
			<template #header>
				<div class="d-flex align-items-center justify-content-between w-100 py-1">
					<div class="d-flex align-items-center gap-2">
						<i class="fa-solid fa-layer-group text-primary"></i>
						<h3 class="block-title mb-0">Grupos Registrados</h3>
					</div>
					<span v-if="selectedCycleName" class="badge bg-primary-subtle text-primary fs-xs">
						Ciclo {{ selectedCycleName }}
					</span>
				</div>
			</template>

			<div class="table-responsive">
				<table class="table table-striped table-hover mb-0 align-middle">
					<thead>
						<tr class="text-uppercase fs-xs">
							<th>Grupo</th>
							<th>Grado</th>
							<th class="text-center">Color</th>
							<th>Turno</th>
							<th class="text-center">Alumnos</th>
							<th class="text-center">Materias</th>
							<th style="min-width: 180px">Cobertura</th>
							<th>Salón</th>
							<th class="text-end">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="g in filteredGroups" :key="g.id">
							<td class="fw-semibold">{{ g.name || g.group_label }}</td>
							<td>{{ g.grade }}</td>
							<td class="text-center">
								<span
									v-if="g.color"
									class="d-inline-block rounded border"
									:title="g.color"
									:style="{
										width: '22px',
										height: '22px',
										backgroundColor: g.color,
									}"
								></span>
								<span v-else class="text-muted">—</span>
							</td>
							<td>
								<span
									:class="[
										'badge rounded-pill',
										g.shift === 'afternoon' ? 'bg-warning text-dark' : 'bg-info',
									]"
								>
									{{ g.shift === 'afternoon' ? 'Vespertino' : 'Matutino' }}
								</span>
							</td>
							<td class="text-center">{{ g.students_count ?? 0 }}</td>
							<td class="text-center">{{ g.subjects_count ?? 0 }}</td>
							<td>
								<div class="d-flex align-items-center gap-2">
									<span
										class="fs-xs fw-semibold"
										style="min-width: 36px"
										:class="coverageTextClass(g.coverage_percent)"
									>
										{{ Math.min(100, Number(g.coverage_percent || 0)) }}%
									</span>
									<div class="progress flex-grow-1" style="height: 8px">
										<div
											class="progress-bar"
											:class="coverageBarClass(g.coverage_percent)"
											role="progressbar"
											:style="{ width: `${Math.min(100, Number(g.coverage_percent || 0))}%` }"
										/>
									</div>
								</div>
							</td>
							<td class="text-muted">{{ g.room_name || '-' }}</td>
							<td class="text-end">
								<div class="btn-group">
									<button type="button" class="btn btn-sm btn-alt-secondary btn-icon-center" disabled>
										<i class="fa-regular fa-calendar"></i>
										Horario
									</button>
									<button
										type="button"
										class="btn btn-sm btn-alt-warning"
										@click="openEditModal(g)"
										title="Editar"
									>
										<i class="fa-solid fa-pen"></i>
									</button>
									<button
										type="button"
										class="btn btn-sm btn-alt-danger"
										@click="confirmDelete(g)"
										title="Eliminar"
									>
										<i class="fa-solid fa-trash"></i>
									</button>
								</div>
							</td>
						</tr>
						<tr v-if="!filteredGroups.length">
							<td colspan="9" class="text-center text-muted py-5">
								<i class="fa-solid fa-folder-open fa-2x d-block mb-2 opacity-50"></i>
								Sin grupos registrados
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</BaseBlock>
	</div>

	<!-- Modal Nuevo / Editar Grupo -->
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
						<i class="fa-solid fa-users-gear text-primary"></i>
						{{ modalType === 'add' ? 'Nuevo Grupo' : 'Editar Grupo' }}
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

							<!-- Nombre del Grupo + Grado -->
							<div class="col-md-6">
								<label class="form-label fw-semibold">Nombre del Grupo</label>
								<input
									class="form-control"
									type="text"
									v-model="form.name"
									placeholder="Ej. 1°A"
									v-uppercase
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

							<div class="col-md-6">
								<label class="form-label fw-semibold">Sección (cluster)</label>
								<select class="form-select" v-model="form.section_id" :disabled="isSaving">
									<option value="">Sin sección</option>
									<option v-for="s in sections" :key="s.id" :value="s.id">
										{{ s.description }}
									</option>
								</select>
							</div>
							<div class="col-md-6 d-flex align-items-end">
								<div v-if="previewColor" class="d-flex align-items-center gap-2 pb-1">
									<span
										class="d-inline-block rounded border"
										:style="{
											width: '28px',
											height: '28px',
											backgroundColor: previewColor,
										}"
									></span>
									<span class="text-muted fs-sm">Color por defecto: {{ previewColor }}</span>
								</div>
							</div>

							<!-- Turno + Capacidad -->
							<div class="col-md-6">
								<label class="form-label fw-semibold">Turno</label>
								<select class="form-select" v-model="form.shift" :disabled="isSaving" required>
									<option value="morning">Matutino</option>
									<option value="afternoon">Vespertino</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="form-label fw-semibold">Capacidad máx. alumnos</label>
								<input
									class="form-control"
									type="number"
									min="1"
									step="1"
									v-model.number="form.student_limit"
									:disabled="isSaving"
									required
								/>
							</div>

							<!-- Año Escolar + Turno (ya está arriba, aquí ciclo) -->
							<div class="col-md-6">
								<label class="form-label fw-semibold">Año Escolar</label>
								<select class="form-select" v-model="form.school_cycle_id" :disabled="isSaving" required>
									<option value="">Selecciona un ciclo...</option>
									<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
										{{ c.description }}
									</option>
								</select>
							</div>

							<!-- Salón / Aula -->
							<div class="col-md-6">
								<label class="form-label fw-semibold">Salón / Aula</label>
								<input
									class="form-control"
									type="text"
									v-model="form.room_name"
									placeholder="Ej. Aula 12-B"
									v-uppercase
									:disabled="isSaving"
								/>
							</div>

							<!-- Número de materias -->
							<div class="col-md-4">
								<label class="form-label fw-semibold">Nº de Materias</label>
								<input
									class="form-control"
									type="number"
									min="0"
									max="20"
									step="1"
									v-model.number="form.subjects_count"
									:disabled="isSaving"
								/>
							</div>

							<!-- Acciones -->
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
									{{ modalType === 'add' ? 'Guardar Grupo' : 'Actualizar Grupo' }}
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

const GROUP_COLORS = {
	1: { A: '#00FFFF', B: '#FFE4C4', C: '#8A2BE2', D: '#7FFF00', E: '#008B8B', F: '#A9A9A9' },
	2: { A: '#008000', B: '#A52A2A', C: '#D2691E', D: '#BC8F8F', E: '#B8860B', F: '#BDB76B' },
	3: { A: '#DEB887', B: '#FF7F50', C: '#6495ED', D: '#87CEFA', E: '#20B2AA', F: '#FFC0CB' },
};

const groups = ref([]);
const grades = ref([]);
const sections = ref([]);
const schoolCycles = ref([]);
const selectedSchoolCycleId = ref(null);

const modalType = ref('add');
const rowSelected = ref(null);
const isSaving = ref(false);

const form = reactive({
	name: '',
	grade_id: '',
	section_id: '',
	school_cycle_id: '',
	shift: 'morning',
	student_limit: 40,
	room_name: '',
	subjects_count: 8,
});

const previewColor = computed(() => resolvePreviewColor());

function resolveDegreeFromGradeId(gradeId) {
	const g = grades.value.find((x) => x.id === gradeId);
	if (!g) return null;
	const order = Number(g.order);
	if (order >= 1 && order <= 3) return order;
	const parsed = parseInt(String(g.description || '').replace(/\D/g, ''), 10);
	return parsed >= 1 && parsed <= 3 ? parsed : null;
}

function resolveClusterLetter() {
	const section = sections.value.find((s) => s.id === form.section_id);
	if (section?.description) {
		const letter = String(section.description).trim().toUpperCase();
		if (letter.length === 1 && letter >= 'A' && letter <= 'F') return letter;
	}
	const match = String(form.name || '').match(/(\d)\s*[°º]?\s*([A-F])/i);
	return match ? match[2].toUpperCase() : null;
}

function resolvePreviewColor() {
	const degree = resolveDegreeFromGradeId(form.grade_id);
	const cluster = resolveClusterLetter();
	if (!degree || !cluster) return null;
	return GROUP_COLORS[degree]?.[cluster] ?? null;
}

const selectedCycleName = computed(() => {
	if (!selectedSchoolCycleId.value) return '';
	const c = schoolCycles.value.find((c) => c.id === selectedSchoolCycleId.value);
	return c ? c.description : '';
});

const filteredGroups = computed(() => {
	if (!selectedSchoolCycleId.value) return groups.value;
	return groups.value.filter((g) => g.school_cycle_id === selectedSchoolCycleId.value);
});

function coverageBarClass(p) {
	const n = Number(p || 0);
	if (n >= 90) return 'bg-success';
	if (n >= 70) return 'bg-warning';
	return 'bg-danger';
}

function coverageTextClass(p) {
	const n = Number(p || 0);
	if (n >= 90) return 'text-success';
	if (n >= 70) return 'text-warning';
	return 'text-danger';
}

async function fetchLists() {
	const [g, c, s] = await Promise.all([
		api.get('/lists/grades'),
		api.get('/lists/school-cycles'),
		api.get('/sections'),
	]);
	grades.value = g.data;
	schoolCycles.value = c.data;
	sections.value = s.data?.data || s.data || [];
}

async function fetchGroups() {
	const { data } = await api.get('/academic-groups');
	groups.value = data.data;
}

function resetForm() {
	Object.assign(form, {
		name: '',
		grade_id: '',
		section_id: '',
		school_cycle_id: selectedSchoolCycleId.value || '',
		shift: 'morning',
		student_limit: 40,
		room_name: '',
		subjects_count: 8,
	});
}

function openCreateModal() {
	modalType.value = 'add';
	rowSelected.value = null;
	resetForm();
	showModal();
}

function openEditModal(g) {
	modalType.value = 'edit';
	rowSelected.value = g;
	Object.assign(form, {
		name: g.name || g.group_label || '',
		grade_id: g.grade_id,
		section_id: g.section_id || '',
		school_cycle_id: g.school_cycle_id,
		shift: g.shift || 'morning',
		student_limit: g.student_limit || 40,
		room_name: g.room_name || '',
		subjects_count: g.subjects_count ?? 0,
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
		const count = Math.max(0, Math.min(20, Number(form.subjects_count || 0)));
		const subjectsArray = Array.from({ length: count }, () => '');

		const payload = {
			name: form.name,
			grade_id: form.grade_id,
			section_id: form.section_id || null,
			school_cycle_id: form.school_cycle_id,
			shift: form.shift,
			room_name: form.room_name || null,
			student_limit: form.student_limit,
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
			title: modalType.value === 'add' ? 'Grupo creado' : 'Grupo actualizado',
			text: form.name,
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
	Swal.fire({
		title: '¿Eliminar grupo?',
		text: g.name || g.group_label,
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
			Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el grupo.' });
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
</style>

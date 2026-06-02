<template>
	<BasePageHeading
		title="Vista previa de horario"
		subtitle="Lista por registro (más reciente arriba) y reporte en cuadrícula por docente"
	>
		<template #extra>
			<div class="d-flex flex-wrap gap-2 align-items-center">
				<label class="form-label mb-0 me-1 text-muted fs-sm">Período</label>
				<select
					class="form-select form-select-sm w-auto"
					v-model="selectedSchoolCycleId"
					:disabled="isLoadingLookups"
				>
					<option disabled value="">— Seleccionar —</option>
					<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
						{{ c.description }}
					</option>
				</select>
				<button
					type="button"
					class="btn btn-sm btn-alt-secondary"
					@click="loadAssignments"
					:disabled="isLoadingList"
				>
					<i class="fa-solid fa-rotate" :class="{ 'fa-spin': isLoadingList }"></i>
					Actualizar
				</button>
			</div>
		</template>
	</BasePageHeading>

	<div class="content">
		<div v-if="!selectedSchoolCycleId" class="alert alert-info d-flex align-items-center gap-2">
			<i class="fa-solid fa-circle-info"></i>
			<span>Seleccione un período escolar para crear asignaciones manuales.</span>
		</div>

		<BaseBlock content-full>
			<ul class="nav nav-tabs nav-tabs-block" role="tablist">
				<li class="nav-item">
					<button
						type="button"
						class="nav-link"
						:class="{ active: activeTab === 'list' }"
						@click="activeTab = 'list'"
					>
						<i class="fa-solid fa-list me-1"></i>
						Lista de asignaciones
					</button>
				</li>
				<li class="nav-item">
					<button
						type="button"
						class="nav-link"
						:class="{ active: activeTab === 'grid' }"
						@click="activeTab = 'grid'"
					>
						<i class="fa-solid fa-table me-1"></i>
						Reporte de horario
					</button>
				</li>
				<li class="nav-item">
					<button
						type="button"
						class="nav-link"
						:class="{ active: activeTab === 'manual' }"
						@click="activeTab = 'manual'"
					>
						<i class="fa-solid fa-pen-to-square me-1"></i>
						Asignación manual
					</button>
				</li>
			</ul>

			<!-- Lista -->
			<div v-show="activeTab === 'list'" class="pt-4">
				<div class="row g-3 mb-4 align-items-center">
					<div class="col-md-6">
						<div class="input-group">
							<span class="input-group-text bg-body border-end-0">
								<i class="fa-solid fa-magnifying-glass text-muted"></i>
							</span>
							<input
								v-model="searchQuery"
								type="search"
								class="form-control border-start-0"
								placeholder="Buscar docente o materia..."
							/>
						</div>
					</div>
					<div class="col-md-6">
						<div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2">
							<span class="badge bg-success-subtle text-success">
								<i class="fa-solid fa-check me-1"></i>
								{{ filteredAssignments.length }} activas
							</span>
							<span class="text-muted fs-sm d-none d-md-inline">
								Orden: último registro arriba
							</span>
							<button
								type="button"
								class="btn btn-alt-primary"
								:disabled="isLoadingList"
								@click="activeTab = 'grid'"
							>
								<i class="fa-solid fa-table me-1"></i>
								Ver cuadrícula
							</button>
						</div>
					</div>
				</div>

				<div class="table-responsive">
					<table class="table table-striped table-hover align-middle mb-0">
						<thead>
							<tr class="text-uppercase fs-xs text-muted">
								<th>Docente</th>
								<th>Materia</th>
								<th>Grupo</th>
								<th class="text-center">Horas / sem</th>
								<th>Turno</th>
								<th>Día</th>
								<th>Hora</th>
								<th>Tipo</th>
								<th class="text-end">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="a in filteredAssignments" :key="a.id">
								<td class="fw-semibold">{{ a.teacher_name || '—' }}</td>
								<td>{{ a.subject_name || '—' }}</td>
								<td>{{ a.cluster_name || '—' }}</td>
								<td class="text-center text-muted">{{ hoursLabel(a) }}</td>
								<td>
									<span class="badge rounded-pill bg-info">{{ shiftLabel(a.shift) }}</span>
								</td>
								<td>{{ dayLabel(a.day_of_week) }}</td>
								<td class="text-nowrap">{{ a.time_slot }}</td>
								<td>
									<span
										class="badge rounded-pill"
										:class="a.assignment_type === 'auto' ? 'bg-primary' : 'bg-secondary'"
									>
										{{ a.assignment_type === 'auto' ? 'Automática' : 'Manual' }}
									</span>
								</td>
								<td class="text-end">
									<div class="d-inline-flex gap-1">
										<button
											type="button"
											class="btn btn-sm btn-warning"
											@click="openEditModal(a)"
											title="Editar"
										>
											<i class="fa-solid fa-pencil"></i>
										</button>
										<button
											type="button"
											class="btn btn-sm btn-alt-danger"
											@click="confirmRemove(a)"
											title="Quitar"
										>
											<i class="fa-solid fa-trash"></i>
											Quitar
										</button>
									</div>
								</td>
							</tr>
							<tr v-if="!filteredAssignments.length">
								<td colspan="9" class="text-center text-muted py-5">
									<i class="fa-solid fa-folder-open fa-2x d-block mb-2 opacity-50"></i>
									No hay asignaciones registradas
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Reporte cuadrícula -->
			<div v-show="activeTab === 'grid'" class="pt-4">
				<TeachingScheduleGridPreview
					ref="gridPreviewRef"
					:initial-shift="previewShift"
					@export-pdf="openSchedulePreviewPdf"
				/>
			</div>

			<!-- Manual -->
			<div v-show="activeTab === 'manual'" class="pt-4">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="card bg-body-tertiary border-0 shadow-sm">
							<div class="card-header border-0 pb-0">
								<h5 class="mb-0">
									<i class="fa-solid fa-calendar-plus text-primary me-2"></i>
									Nueva asignación manual
								</h5>
								<p class="text-muted fs-sm mb-0 mt-2">
									Al guardar se valida que el docente y el grupo no tengan otro clase en el mismo
									horario, que el docente tenga disponibilidad marcada en ese día y franja, y que
									cuenta con horas disponibles (1h por franja). Al elegir docente, materia y grupo
									se toman de Asignación de materias; SERVICIO usa automáticamente SIN GRUPO.
								</p>
							</div>
							<div class="card-body">
								<TeachingAssignmentForm
									:form="manual"
									:teachers="teachers"
									:specialties="specialties"
									:academic-groups="academicGroupsForCycle"
									:subject-links="subjectLinks"
									:sin-grupo-group-ids="sinGrupoGroupIds"
									:day-options="DAY_OPTIONS"
									:morning-slots="MORNING_SLOTS"
									:evening-slots="EVENING_SLOTS"
									:disabled="isSaving || !selectedSchoolCycleId"
									@submit="saveManual"
									@reset="resetManual"
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</BaseBlock>
	</div>

	<!-- Modal Editar -->
	<div
		class="modal fade"
		id="modal-edit-assignment"
		tabindex="-1"
		aria-labelledby="modal-edit-assignment-label"
		aria-hidden="true"
		data-bs-backdrop="static"
	>
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title d-flex align-items-center gap-2" id="modal-edit-assignment-label">
						<i class="fa-solid fa-pencil text-warning"></i>
						Editar asignación
					</h5>
					<button
						type="button"
						class="btn-close"
						data-bs-dismiss="modal"
						aria-label="Close"
						@click="closeEditModal"
					></button>
				</div>
				<div class="modal-body">
					<p class="text-muted fs-sm">
						Al guardar se valida que el docente y el grupo no tengan otra clase en el mismo horario,
						que el docente tenga disponibilidad marcada en ese día y franja, y que cuenta con horas
						disponibles (1h por franja).
					</p>
					<TeachingAssignmentForm
						v-if="editingId"
						:form="editForm"
						:teachers="teachers"
						:specialties="specialties"
						:academic-groups="academicGroupsForCycle"
						:subject-links="subjectLinks"
						:sin-grupo-group-ids="sinGrupoGroupIds"
						:day-options="DAY_OPTIONS"
						:morning-slots="MORNING_SLOTS"
						:evening-slots="EVENING_SLOTS"
						:disabled="isSaving || !selectedSchoolCycleId"
						submit-label="Guardar cambios"
						@submit="saveEdit"
						@reset="resetEditForm"
					/>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import api from '@/services/api';
import TeachingAssignmentForm from '@/components/TeachingAssignmentForm.vue';
import TeachingScheduleGridPreview from '@/components/TeachingScheduleGridPreview.vue';
import Swal from 'sweetalert2';

const CYCLE_STORAGE_KEY = 'subject-assignment-school-cycle-id';

const MORNING_SLOTS = [
	'7:30-8:20',
	'8:20-9:10',
	'9:10-10:00',
	'10:00-10:50',
	'11:10-12:00',
	'12:00-12:50',
	'12:50-13:40',
];

const EVENING_SLOTS = [
	'14:00-14:50',
	'14:50-15:40',
	'15:40-16:30',
	'16:30-17:20',
	'17:20-18:10',
	'18:10-19:00',
	'19:00-19:50',
	'19:50-20:40',
];

const DAY_OPTIONS = [
	{ value: 'lunes', label: 'Lunes' },
	{ value: 'martes', label: 'Martes' },
	{ value: 'miercoles', label: 'Miércoles' },
	{ value: 'jueves', label: 'Jueves' },
	{ value: 'viernes', label: 'Viernes' },
];

const activeTab = ref('list');
const previewShift = ref('morning');
const gridPreviewRef = ref(null);
const searchQuery = ref('');
const isLoadingList = ref(false);
const isSaving = ref(false);
const isExportingPdf = ref(false);
const editingId = ref(null);

const assignments = ref([]);
const schoolCycles = ref([]);
const selectedSchoolCycleId = ref('');
const teachers = ref([]);
const specialties = ref([]);
const academicGroups = ref([]);
const subjectLinks = ref([]);
const sinGrupoGroupIds = ref({});
const isLoadingLookups = ref(false);

const manual = reactive(getEmptyForm());
const editForm = reactive(getEmptyForm());

const sortedAssignments = computed(() =>
	[...assignments.value].sort((a, b) => Number(b.id) - Number(a.id))
);

const filteredAssignments = computed(() => {
	const q = searchQuery.value?.toLowerCase().trim() || '';
	const rows = sortedAssignments.value;
	if (!q) return rows;
	return rows.filter((a) => {
		const hay = `${a.teacher_name || ''} ${a.subject_name || ''} ${a.cluster_name || ''}`.toLowerCase();
		return hay.includes(q);
	});
});

const academicGroupsForCycle = computed(() => {
	if (!selectedSchoolCycleId.value) {
		return academicGroups.value;
	}

	return academicGroups.value.filter(
		(group) => Number(group.school_cycle_id) === Number(selectedSchoolCycleId.value)
	);
});

function hoursLabel(assignment) {
	const hours = Number(assignment.specialty_hours_per_week);
	if (hours > 0) {
		return `${hours}h`;
	}
	return '1h';
}

function getEmptyForm() {
	return {
		teacher_id: '',
		specialty_id: '',
		academic_group_id: '',
		shift: 'morning',
		day_of_week: 'lunes',
		time_slot: MORNING_SLOTS[0],
	};
}

function shiftLabel(shift) {
	return shift === 'afternoon' ? 'Vespertino' : 'Matutino';
}

function dayLabel(day) {
	const d = DAY_OPTIONS.find((x) => x.value === day);
	return d ? d.label : day || '—';
}

function buildPayload(form) {
	return {
		teacher_id: Number(form.teacher_id),
		specialty_id: Number(form.specialty_id),
		academic_group_id: Number(form.academic_group_id),
		shift: form.shift,
		day_of_week: form.day_of_week,
		time_slot: form.time_slot,
		assignment_type: 'manual',
	};
}

function fillForm(target, assignment) {
	target.teacher_id = assignment.teacher_id || '';
	target.specialty_id = assignment.specialty_id || '';
	target.academic_group_id = assignment.academic_group_id || '';
	target.shift = assignment.shift || 'morning';
	target.day_of_week = assignment.day_of_week || 'lunes';
	target.time_slot = assignment.time_slot || MORNING_SLOTS[0];
}

async function loadAssignments() {
	isLoadingList.value = true;
	try {
		const { data } = await api.get('/teaching-assignments');
		assignments.value = data.data || [];
	} catch {
		assignments.value = [];
		Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar la lista de asignaciones.' });
	} finally {
		isLoadingList.value = false;
	}
}

async function loadLookups() {
	isLoadingLookups.value = true;
	try {
		const [cycleRes, tRes, sRes, gRes] = await Promise.all([
			api.get('/lists/school-cycles'),
			api.get('/teachers'),
			api.get('/specialties'),
			api.get('/academic-groups'),
		]);
		schoolCycles.value = cycleRes.data.data ?? cycleRes.data ?? [];
		teachers.value = tRes.data.data || [];
		specialties.value = sRes.data.data || [];
		academicGroups.value = gRes.data.data || [];

		if (!selectedSchoolCycleId.value && schoolCycles.value.length) {
			const stored = localStorage.getItem(CYCLE_STORAGE_KEY);
			const match = schoolCycles.value.find((cycle) => String(cycle.id) === stored);
			selectedSchoolCycleId.value = match ? match.id : schoolCycles.value[0].id;
		}
	} catch {
		Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar catálogos (docentes, materias, grupos).' });
	} finally {
		isLoadingLookups.value = false;
	}
}

async function loadFormContext() {
	if (!selectedSchoolCycleId.value) {
		subjectLinks.value = [];
		sinGrupoGroupIds.value = {};
		return;
	}

	try {
		const { data } = await api.get('/teaching-assignments/form-context', {
			params: { school_cycle_id: selectedSchoolCycleId.value },
		});

		subjectLinks.value = data.data?.subject_links || [];
		sinGrupoGroupIds.value = data.data?.sin_grupo_group_ids || {};
	} catch {
		subjectLinks.value = [];
		sinGrupoGroupIds.value = {};
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'No se pudo cargar el contexto de asignación de materias.',
		});
	}
}

function resetManual() {
	Object.assign(manual, getEmptyForm());
}

function resetEditForm() {
	if (!editingId.value) return;
	const assignment = assignments.value.find((a) => a.id === editingId.value);
	if (assignment) fillForm(editForm, assignment);
}

function showEditModal() {
	const el = document.getElementById('modal-edit-assignment');
	if (el) bootstrap.Modal.getOrCreateInstance(el).show();
}

function closeEditModal() {
	const el = document.getElementById('modal-edit-assignment');
	if (el) bootstrap.Modal.getOrCreateInstance(el)?.hide();
	editingId.value = null;
}

function openEditModal(assignment) {
	editingId.value = assignment.id;
	fillForm(editForm, assignment);
	showEditModal();
}

async function saveManual() {
	isSaving.value = true;
	try {
		const { data } = await api.post('/teaching-assignments', buildPayload(manual), { showErrors: false });

		Toast.fire({ icon: 'success', title: data.message || 'Guardado correctamente' });
		resetManual();
		await loadAssignments();
		gridPreviewRef.value?.loadPreview?.();
		activeTab.value = 'list';
	} catch (err) {
		showSaveError(err);
	} finally {
		isSaving.value = false;
	}
}

async function saveEdit() {
	if (!editingId.value) return;

	isSaving.value = true;
	try {
		const { data } = await api.put(
			`/teaching-assignments/${editingId.value}`,
			buildPayload(editForm),
			{ showErrors: false }
		);

		Toast.fire({ icon: 'success', title: data.message || 'Actualizado correctamente' });
		closeEditModal();
		await loadAssignments();
		gridPreviewRef.value?.loadPreview?.();
	} catch (err) {
		showSaveError(err);
	} finally {
		isSaving.value = false;
	}
}

function showSaveError(err) {
	const json = err?.data || {};
	const msg = json.message;
	const text =
		typeof msg === 'string' ? msg : 'No se pudo guardar. Revise disponibilidad, horas y conflictos de horario.';
	Swal.fire({ icon: 'error', title: 'No se puede guardar', text });
}

async function openSchedulePreviewPdf(shift = previewShift.value) {
	previewShift.value = shift;
	isExportingPdf.value = true;
	try {
		const res = await api.get('/teaching-assignments/schedule-preview/pdf', {
			params: { shift },
			responseType: 'blob',
			headers: { Accept: 'application/pdf' },
		});

		const blob = new Blob([res.data], { type: 'application/pdf' });
		const url = URL.createObjectURL(blob);
		window.open(url, '_blank');
		setTimeout(() => URL.revokeObjectURL(url), 60_000);
	} catch {
		Swal.fire({
			icon: 'error',
			title: 'No se pudo generar el PDF',
			text: 'Verifique que existan asignaciones activas en turno matutino.',
		});
	} finally {
		isExportingPdf.value = false;
	}
}

function confirmRemove(a) {
	Swal.fire({
		title: '¿Quitar esta asignación?',
		text: `${a.teacher_name} — ${a.subject_name} — ${a.time_slot}`,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Sí, quitar',
		cancelButtonText: 'Cancelar',
	}).then(async (result) => {
		if (!result.isConfirmed) return;
		try {
			await api.delete(`/teaching-assignments/${a.id}`);
			Toast.fire({ icon: 'success', title: 'Asignación eliminada' });
			await loadAssignments();
			gridPreviewRef.value?.loadPreview?.();
		} catch {
			Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar la asignación.' });
		}
	});
}

watch(activeTab, (tab) => {
	if (tab === 'grid') {
		gridPreviewRef.value?.loadPreview?.();
	}
});

watch(selectedSchoolCycleId, async (cycleId) => {
	if (!cycleId) {
		return;
	}

	localStorage.setItem(CYCLE_STORAGE_KEY, String(cycleId));
	await loadFormContext();
	resetManual();
});

onMounted(async () => {
	await loadLookups();
	await loadFormContext();
	await loadAssignments();
});
</script>

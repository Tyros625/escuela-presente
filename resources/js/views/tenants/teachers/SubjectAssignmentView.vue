<template>
	<BasePageHeading
		title="Asignación de materias"
		subtitle="Asigne docentes a cada grupo y materia. Después podrá crear el horario en Horarios."
	>
		<template #extra>
			<div class="d-flex flex-wrap gap-2 align-items-center">
				<label class="form-label mb-0 me-1 text-muted fs-sm">Período</label>
				<select
					class="form-select form-select-sm ep-period-select"
					v-model="selectedSchoolCycleId"
					:disabled="isLoading"
				>
					<option disabled value="">— Seleccionar —</option>
					<option v-for="c in schoolCycles" :key="c.id" :value="c.id">
						{{ c.description }}
					</option>
				</select>
				<button
					type="button"
					class="btn btn-sm btn-alt-secondary"
					:disabled="isLoading || !selectedSchoolCycleId"
					@click="loadMatrix"
				>
					<i class="fa-solid fa-rotate" :class="{ 'fa-spin': isLoading }"></i>
					Actualizar
				</button>
			</div>
		</template>
	</BasePageHeading>

	<div class="content">
		<div v-if="!selectedSchoolCycleId" class="alert alert-info d-flex align-items-center gap-2">
			<i class="fa-solid fa-circle-info"></i>
			<span>Seleccione un período escolar para comenzar.</span>
		</div>

		<template v-else>
			<div class="row g-3 mb-4">
				<div class="col-md-4">
					<div class="block block-rounded mb-0 h-100">
						<div class="block-content block-content-full">
							<div class="fs-sm text-muted mb-1">Progreso general</div>
							<div class="fs-3 fw-bold">{{ stats.completion_percent }}%</div>
							<div class="progress mt-2" style="height: 8px">
								<div
									class="progress-bar"
									:class="stats.completion_percent === 100 ? 'bg-success' : 'bg-primary'"
									:style="{ width: `${stats.completion_percent}%` }"
								></div>
							</div>
							<div class="fs-xs text-muted mt-2">
								{{ stats.filled_cells }} de {{ stats.total_cells }} celdas asignadas
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="block block-rounded mb-0 h-100">
						<div class="block-content block-content-full">
							<div class="fs-sm text-muted mb-1">Docentes disponibles</div>
							<div class="fs-3 fw-bold">{{ teachers.length }}</div>
							<div class="fs-xs text-muted mt-2">
								Pase el cursor sobre un nombre para ver su especialidad.
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="block block-rounded mb-0 h-100">
						<div class="block-content block-content-full d-flex flex-column justify-content-center">
							<RouterLink
								:to="{ name: 'teaching-assignments' }"
								class="btn btn-sm btn-alt-primary"
							>
								<i class="fa-solid fa-clock me-1"></i>
								Ir a crear horarios
							</RouterLink>
							<div class="fs-xs text-muted mt-2">
								Complete las materias aquí antes de definir día y hora.
							</div>
						</div>
					</div>
				</div>
			</div>

			<div v-if="isLoading" class="text-center py-5 text-muted">
				<i class="fa fa-cog fa-spin fa-2x mb-3"></i>
				<p class="mb-0">Cargando matriz de asignación...</p>
			</div>

			<div v-else-if="!gradeBlocks.length" class="alert alert-warning">
				<p class="mb-2 fw-semibold">No hay datos para mostrar en este período.</p>
				<ul class="mb-0 ps-3">
					<li>Registre <RouterLink :to="{ name: 'academic-groups' }">grupos académicos</RouterLink>.</li>
					<li>Configure <RouterLink :to="{ name: 'specialties' }">materias por grado</RouterLink>.</li>
				</ul>
			</div>

			<template v-else>
				<SubjectAssignmentGradeTable
					v-for="gradeBlock in gradeBlocks"
					:key="gradeBlock.grade_id"
					:grade-block="gradeBlock"
					:teachers="teachers"
					:cell-values="cellValues"
					@cell-change="onCellChange"
				/>
			</template>
		</template>

		<div
			v-if="hasChanges"
			class="subject-assignment-save-bar shadow-lg"
		>
			<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-4 py-3">
				<div>
					<span class="fw-semibold">{{ pendingChangeCount }} cambio(s) sin guardar</span>
					<span class="text-muted fs-sm d-block">Los cambios no se aplican hasta guardar.</span>
				</div>
				<div class="d-flex gap-2">
					<button
						type="button"
						class="btn btn-alt-secondary"
						:disabled="isSaving"
						@click="confirmDiscard"
					>
						Cancelar
					</button>
					<button
						type="button"
						class="btn btn-primary"
						:disabled="isSaving"
						@click="saveChanges"
					>
						<i class="fa fa-cog fa-spin me-1" v-if="isSaving"></i>
						<i class="fa-solid fa-floppy-disk me-1" v-else></i>
						Guardar grupo
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { onBeforeRouteLeave } from 'vue-router';
import Swal from 'sweetalert2';
import api from '@/services/api';
import SubjectAssignmentGradeTable from '@/components/SubjectAssignmentGradeTable.vue';
import { cellKey, parseCellKey } from '@/utils/subjectAssignment';

const CYCLE_STORAGE_KEY = 'subject-assignment-school-cycle-id';

const schoolCycles = ref([]);
const teachers = ref([]);
const selectedSchoolCycleId = ref('');
const gradeBlocks = ref([]);
const cellValues = ref({});
const originalValues = ref({});
const stats = ref({
	total_cells: 0,
	filled_cells: 0,
	completion_percent: 0,
});
const isLoading = ref(false);
const isSaving = ref(false);

const hasChanges = computed(() => {
	const keys = new Set([
		...Object.keys(cellValues.value),
		...Object.keys(originalValues.value),
	]);

	for (const key of keys) {
		const current = cellValues.value[key] ?? null;
		const original = originalValues.value[key] ?? null;
		if (String(current ?? '') !== String(original ?? '')) {
			return true;
		}
	}

	return false;
});

const pendingChangeCount = computed(() => buildChanges().length);

function onCellChange({ groupId, specialtyId, teacherId }) {
	const key = cellKey(groupId, specialtyId);
	const next = { ...cellValues.value };

	if (!teacherId) {
		delete next[key];
	} else {
		next[key] = teacherId;
	}

	cellValues.value = next;
}

function buildChanges() {
	const changes = [];
	const keys = new Set([
		...Object.keys(cellValues.value),
		...Object.keys(originalValues.value),
	]);

	for (const key of keys) {
		const current = cellValues.value[key] ?? null;
		const original = originalValues.value[key] ?? null;

		if (String(current ?? '') === String(original ?? '')) {
			continue;
		}

		const parsed = parseCellKey(key);
		if (!parsed) {
			continue;
		}

		changes.push({
			academic_group_id: parsed.groupId,
			specialty_id: parsed.specialtyId,
			teacher_id: current,
		});
	}

	return changes;
}

function syncFromMatrix(data) {
	const values = {};

	for (const [key, assignment] of Object.entries(data.assignments || {})) {
		if (assignment?.teacher_id) {
			values[key] = assignment.teacher_id;
		}
	}

	cellValues.value = { ...values };
	originalValues.value = { ...values };
	gradeBlocks.value = data.grades || [];
	stats.value = data.stats || {
		total_cells: 0,
		filled_cells: 0,
		completion_percent: 0,
	};
}

async function fetchSchoolCycles() {
	const { data } = await api.get('/lists/school-cycles');
	schoolCycles.value = data.data ?? data;

	if (!selectedSchoolCycleId.value && schoolCycles.value.length) {
		const stored = localStorage.getItem(CYCLE_STORAGE_KEY);
		const match = schoolCycles.value.find((c) => String(c.id) === stored);
		selectedSchoolCycleId.value = match ? match.id : schoolCycles.value[0].id;
	}
}

async function fetchTeachers() {
	const { data } = await api.get('/teachers');
	teachers.value = data.data || [];
}

async function loadMatrix() {
	if (!selectedSchoolCycleId.value) {
		return;
	}

	isLoading.value = true;

	try {
		const { data } = await api.get('/group-subject-assignments/matrix', {
			params: { school_cycle_id: selectedSchoolCycleId.value },
		});

		syncFromMatrix(data.data);
	} finally {
		isLoading.value = false;
	}
}

async function handleCycleChange(newCycleId, previousCycleId) {
	if (!newCycleId) {
		return;
	}

	if (hasChanges.value && previousCycleId && previousCycleId !== newCycleId) {
		const result = await Swal.fire({
			title: '¿Cambiar período?',
			text: 'Tiene cambios sin guardar que se perderán.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, cambiar',
			cancelButtonText: 'Cancelar',
		});

		if (!result.isConfirmed) {
			selectedSchoolCycleId.value = previousCycleId;
			return;
		}
	}

	localStorage.setItem(CYCLE_STORAGE_KEY, String(newCycleId));
	await loadMatrix();
}

async function saveChanges() {
	const changes = buildChanges();

	if (!changes.length) {
		return;
	}

	isSaving.value = true;

	try {
		const { data } = await api.put(
			'/group-subject-assignments/bulk',
			{
				school_cycle_id: selectedSchoolCycleId.value,
				changes,
			},
			{ showErrors: false }
		);

		await loadMatrix();

		const warnings = data.data?.warnings || [];
		const warningHtml =
			warnings.length > 0
				? `<ul class="text-start mb-0 mt-2">${warnings.map((w) => `<li>${w}</li>`).join('')}</ul>`
				: '';

		Swal.fire({
			icon: warnings.length ? 'warning' : 'success',
			title: data.message || 'Asignaciones guardadas',
			html: warningHtml || undefined,
		});
	} catch (error) {
		const msg =
			error.response?.data?.message ||
			error.response?.data?.errors ||
			'No se pudieron guardar las asignaciones.';

		Swal.fire({
			icon: 'error',
			title: 'Error al guardar',
			text: typeof msg === 'string' ? msg : 'Revise la carga horaria de los docentes.',
		});
	} finally {
		isSaving.value = false;
	}
}

function confirmDiscard() {
	Swal.fire({
		title: '¿Descartar cambios?',
		text: 'Se restaurará la última versión guardada.',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Descartar',
		cancelButtonText: 'Seguir editando',
	}).then((result) => {
		if (result.isConfirmed) {
			cellValues.value = { ...originalValues.value };
		}
	});
}

function onBeforeUnload(event) {
	if (hasChanges.value) {
		event.preventDefault();
		event.returnValue = '';
	}
}

onBeforeRouteLeave((_to, _from, next) => {
	if (!hasChanges.value) {
		next();
		return;
	}

	Swal.fire({
		title: '¿Salir sin guardar?',
		text: 'Los cambios en la matriz se perderán.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Salir',
		cancelButtonText: 'Quedarme',
	}).then((result) => {
		next(result.isConfirmed);
	});
});

watch(selectedSchoolCycleId, (value, oldValue) => {
	if (!value) {
		return;
	}

	handleCycleChange(value, oldValue || '');
});

onMounted(async () => {
	window.addEventListener('beforeunload', onBeforeUnload);
	await Promise.all([fetchSchoolCycles(), fetchTeachers()]);


});

onBeforeUnmount(() => {
	window.removeEventListener('beforeunload', onBeforeUnload);
});
</script>

<style scoped>
.ep-period-select {
	appearance: none;
	-webkit-appearance: none;
	-moz-appearance: none;
	min-width: min(18rem, 100%);
	max-width: 100%;
	padding-right: 2.25rem;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6l4 4 4-4' stroke='%23697386' stroke-width='1.75' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
	background-repeat: no-repeat;
	background-position: right 0.65rem center;
	background-size: 0.875rem;
}

.subject-assignment-save-bar {
	position: fixed;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 1030;
	background: var(--bs-body-bg, #fff);
	border-top: 1px solid var(--bs-border-color, #dfe3ea);
}

.content {
	padding-bottom: 5.5rem;
}
</style>

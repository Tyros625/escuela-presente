<template>
	<BasePageHeading title="Asignación de materias" subtitle="Docentes — carga por grupo y horario">
		<template #extra>
			<button
				type="button"
				class="btn btn-sm btn-alt-secondary"
				@click="loadAssignments"
				:disabled="isLoadingList"
			>
				<i class="fa-solid fa-rotate" :class="{ 'fa-spin': isLoadingList }"></i>
				Actualizar
			</button>
		</template>
	</BasePageHeading>

	<div class="content">
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
				<div class="row g-3 mb-4">
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
					<div class="col-md-6 text-md-end">
						<span class="badge bg-success-subtle text-success me-2">
							<i class="fa-solid fa-check me-1"></i>
							{{ filteredAssignments.length }} activas
						</span>
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
								<td class="text-center text-muted">
									{{ a.specialty_hours_per_week != null ? `${a.specialty_hours_per_week}h` : '—' }}
								</td>
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
									<button
										type="button"
										class="btn btn-sm btn-alt-danger"
										@click="confirmRemove(a)"
										title="Quitar"
									>
										<i class="fa-solid fa-trash"></i>
										Quitar
									</button>
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
									horario y que el docente tenga disponibilidad marcada en ese día y franja.
								</p>
							</div>
							<div class="card-body">
								<form @submit.prevent="saveManual" class="row g-3">
									<div class="col-md-6">
										<label class="form-label fw-semibold">Docente</label>
										<select v-model="manual.teacher_id" class="form-select" required :disabled="isSaving">
											<option disabled value="">— Seleccionar —</option>
											<option v-for="t in teachers" :key="t.id" :value="t.id">
												{{ t.display_name || formatTeacherName(t) }}
											</option>
										</select>
									</div>
									<div class="col-md-6">
										<label class="form-label fw-semibold">Materia</label>
										<select v-model="manual.specialty_id" class="form-select" required :disabled="isSaving">
											<option disabled value="">— Seleccionar —</option>
											<option v-for="s in specialties" :key="s.id" :value="s.id">
												{{ s.description }}
											</option>
										</select>
									</div>
									<div class="col-md-6">
										<label class="form-label fw-semibold">Grupo</label>
										<select
											v-model="manual.academic_group_id"
											class="form-select"
											required
											:disabled="isSaving"
										>
											<option disabled value="">— Seleccionar —</option>
											<option v-for="g in groupsForShift" :key="g.id" :value="g.id">
												{{ g.name || g.group_label || 'Grupo' }} — {{ shiftLabel(g.shift) }}
											</option>
										</select>
										<div v-if="manual.shift && !groupsForShift.length" class="form-text text-warning">
											No hay grupos registrados para este turno.
										</div>
									</div>
									<div class="col-md-6">
										<label class="form-label fw-semibold">Turno</label>
										<select v-model="manual.shift" class="form-select" required :disabled="isSaving">
											<option value="morning">Matutino</option>
											<option value="afternoon">Vespertino</option>
										</select>
									</div>
									<div class="col-md-6">
										<label class="form-label fw-semibold">Día</label>
										<select v-model="manual.day_of_week" class="form-select" required :disabled="isSaving">
											<option v-for="d in DAY_OPTIONS" :key="d.value" :value="d.value">
												{{ d.label }}
											</option>
										</select>
									</div>
									<div class="col-md-6">
										<label class="form-label fw-semibold">Hora (franja)</label>
										<select v-model="manual.time_slot" class="form-select" required :disabled="isSaving">
											<option disabled value="">— Seleccionar —</option>
											<option v-for="slot in slotOptions" :key="slot" :value="slot">
												{{ slot }}
											</option>
										</select>
									</div>
									<div class="col-12 d-flex flex-wrap gap-2 justify-content-end pt-2">
										<button
											type="button"
											class="btn btn-alt-secondary"
											@click="resetManual"
											:disabled="isSaving"
										>
											<i class="fa-solid fa-eraser me-1"></i>
											Limpiar
										</button>
										<button type="submit" class="btn btn-primary" :disabled="isSaving">
											<i class="fa fa-cog fa-spin me-1" v-if="isSaving"></i>
											<i class="fa-solid fa-floppy-disk me-1" v-else></i>
											Guardar asignación
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</BaseBlock>
	</div>
</template>

<script setup>
import api from '@/services/api';
import Swal from 'sweetalert2';

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
const searchQuery = ref('');
const isLoadingList = ref(false);
const isSaving = ref(false);

const assignments = ref([]);
const teachers = ref([]);
const specialties = ref([]);
const academicGroups = ref([]);

const manual = reactive({
	teacher_id: '',
	specialty_id: '',
	academic_group_id: '',
	shift: 'morning',
	day_of_week: 'lunes',
	time_slot: MORNING_SLOTS[0],
});

const slotOptions = computed(() =>
	manual.shift === 'morning' ? MORNING_SLOTS : EVENING_SLOTS
);

const groupsForShift = computed(() =>
	academicGroups.value.filter((g) => g.shift === manual.shift)
);

const filteredAssignments = computed(() => {
	const q = searchQuery.value?.toLowerCase().trim() || '';
	if (!q) return assignments.value;
	return assignments.value.filter((a) => {
		const hay = `${a.teacher_name || ''} ${a.subject_name || ''} ${a.cluster_name || ''}`.toLowerCase();
		return hay.includes(q);
	});
});

function shiftLabel(shift) {
	return shift === 'afternoon' ? 'Vespertino' : 'Matutino';
}

function dayLabel(day) {
	const d = DAY_OPTIONS.find((x) => x.value === day);
	return d ? d.label : day || '—';
}

function formatTeacherName(t) {
	const ln = [t.last_name_father, t.last_name_mother].filter(Boolean).join(' ');
	return ln ? `${ln}, ${t.name || ''}` : t.name || '';
}

watch(
	() => manual.shift,
	() => {
		manual.academic_group_id = '';
		const slots = manual.shift === 'morning' ? MORNING_SLOTS : EVENING_SLOTS;
		if (!slots.includes(manual.time_slot)) {
			manual.time_slot = slots[0] || '';
		}
	}
);

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
	try {
		const [tRes, sRes, gRes] = await Promise.all([
			api.get('/teachers'),
			api.get('/specialties'),
			api.get('/academic-groups'),
		]);
		teachers.value = tRes.data.data || [];
		specialties.value = sRes.data.data || [];
		academicGroups.value = gRes.data.data || [];
	} catch {
		Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar catálogos (docentes, materias, grupos).' });
	}
}

function resetManual() {
	manual.teacher_id = '';
	manual.specialty_id = '';
	manual.academic_group_id = '';
	manual.shift = 'morning';
	manual.day_of_week = 'lunes';
	manual.time_slot = MORNING_SLOTS[0];
}

async function saveManual() {
	isSaving.value = true;
	try {
		const { data } = await api.post(
			'/teaching-assignments',
			{
				teacher_id: Number(manual.teacher_id),
				specialty_id: Number(manual.specialty_id),
				academic_group_id: Number(manual.academic_group_id),
				shift: manual.shift,
				day_of_week: manual.day_of_week,
				time_slot: manual.time_slot,
				assignment_type: 'manual',
			},
			{ showErrors: false }
		);

		Toast.fire({ icon: 'success', title: data.message || 'Guardado correctamente' });
		resetManual();
		await loadAssignments();
		activeTab.value = 'list';
	} catch (err) {
		const json = err?.data || {};
		const msg = json.message;
		const text =
			typeof msg === 'string' ? msg : 'No se pudo guardar. Revise disponibilidad y conflictos de horario.';
		Swal.fire({ icon: 'error', title: 'No se puede guardar', text });
	} finally {
		isSaving.value = false;
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
		} catch {
			Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar la asignación.' });
		}
	});
}

onMounted(async () => {
	await loadLookups();
	await loadAssignments();
});
</script>

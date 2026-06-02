<template>
	<div class="schedule-grid-preview">
		<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
			<div>
				<h5 class="mb-1">Reporte de horario</h5>
				<p class="text-muted fs-sm mb-0">
					Misma vista que el PDF: filas por docente (última asignación arriba dentro de cada materia).
				</p>
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2">
				<select
					v-model="shift"
					class="form-select form-select-sm w-auto"
					:disabled="loading"
					@change="loadPreview"
				>
					<option value="morning">Matutino</option>
					<option value="afternoon">Vespertino</option>
				</select>
				<button
					type="button"
					class="btn btn-sm btn-alt-secondary"
					:disabled="loading"
					@click="loadPreview"
				>
					<i class="fa-solid fa-rotate" :class="{ 'fa-spin': loading }"></i>
				</button>
				<button
					type="button"
					class="btn btn-sm btn-primary"
					:disabled="loading || !report?.teachers?.length"
					@click="$emit('export-pdf', shift)"
				>
					<i class="fa-solid fa-file-pdf me-1"></i>
					Descargar PDF
				</button>
			</div>
		</div>

		<div v-if="loading" class="text-center py-5 text-muted">
			<i class="fa fa-cog fa-spin fa-2x mb-2"></i>
			<p class="mb-0">Generando vista previa...</p>
		</div>

		<div v-else-if="!report?.teachers?.length" class="alert alert-warning mb-0">
			No hay asignaciones activas para el turno {{ shiftLabel }}.
		</div>

		<div v-else class="schedule-grid-scroll">
			<table class="table table-bordered table-sm schedule-grid-table mb-0">
				<thead>
					<tr>
						<th class="sticky-num" rowspan="2">#</th>
						<th class="sticky-teacher" rowspan="2">Docente</th>
						<th class="sticky-subject" rowspan="2">Materia</th>
						<th
							v-for="(label, index) in report.day_labels"
							:key="`day-${index}`"
							:colspan="report.slots_per_day"
							class="text-center text-uppercase fs-xs"
						>
							{{ label }}
						</th>
					</tr>
					<tr>
						<template v-for="day in report.days" :key="`slots-${day}`">
							<th
								v-for="slot in report.slots"
								:key="`${day}-${slot}`"
								class="slot-head text-center"
							>
								{{ slot }}
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr v-for="row in report.teachers" :key="row.number">
						<td class="sticky-num text-center fw-semibold">{{ row.number }}</td>
						<td class="sticky-teacher">{{ row.teacher_name }}</td>
						<td class="sticky-subject">{{ row.subject_name }}</td>
						<template v-for="day in report.days" :key="`row-${row.number}-${day}`">
							<td
								v-for="slot in report.slots"
								:key="`${row.number}-${day}-${slot}`"
								class="grid-slot text-center"
								:style="cellStyle(row.cells?.[day]?.[slot])"
							>
								{{ row.cells?.[day]?.[slot]?.text || '' }}
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script setup>
import api from '@/services/api';

const props = defineProps({
	initialShift: {
		type: String,
		default: 'morning',
	},
});

defineEmits(['export-pdf']);

const shift = ref(props.initialShift);
const loading = ref(false);
const report = ref(null);

const shiftLabel = computed(() => (shift.value === 'afternoon' ? 'vespertino' : 'matutino'));

function cellStyle(cell) {
	if (!cell?.background) {
		return {};
	}

	return {
		backgroundColor: cell.background,
		fontWeight: '600',
	};
}

async function loadPreview() {
	loading.value = true;
	try {
		const { data } = await api.get('/teaching-assignments/schedule-preview', {
			params: { shift: shift.value },
		});
		report.value = data.data;
	} catch {
		report.value = null;
	} finally {
		loading.value = false;
	}
}

onMounted(loadPreview);

defineExpose({ loadPreview, shift });
</script>

<style scoped>
.schedule-grid-scroll {
	overflow: auto;
	max-height: min(70vh, 720px);
	border: 1px solid var(--bs-border-color);
	border-radius: 0.375rem;
}

.schedule-grid-table {
	min-width: max-content;
	font-size: 0.72rem;
}

.schedule-grid-table thead th {
	position: sticky;
	top: 0;
	z-index: 3;
	background: var(--bs-tertiary-bg, #f8f9fa);
	vertical-align: middle;
}

.sticky-num {
	position: sticky;
	left: 0;
	z-index: 5;
	min-width: 2.25rem;
	background: var(--bs-body-bg, #fff);
}

.sticky-teacher {
	position: sticky;
	left: 2.25rem;
	z-index: 5;
	min-width: 11rem;
	max-width: 14rem;
	background: var(--bs-body-bg, #fff);
	white-space: normal;
	line-height: 1.25;
}

.sticky-subject {
	position: sticky;
	left: calc(2.25rem + 11rem);
	z-index: 5;
	min-width: 6.5rem;
	max-width: 9rem;
	background: var(--bs-body-bg, #fff);
	white-space: normal;
	line-height: 1.25;
}

.schedule-grid-table thead .sticky-num,
.schedule-grid-table thead .sticky-teacher,
.schedule-grid-table thead .sticky-subject {
	z-index: 6;
}

.slot-head {
	font-size: 0.62rem;
	min-width: 4.1rem;
	white-space: nowrap;
}

.grid-slot {
	min-width: 4.1rem;
	padding: 0.2rem 0.15rem !important;
	font-size: 0.68rem;
	line-height: 1.15;
}
</style>

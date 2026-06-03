<template>
	<BaseBlock class="mb-4 subject-assignment-grade-block">
		<template #header>
			<div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2 py-1">
				<div class="d-flex align-items-center gap-2">
					<span class="badge bg-primary">{{ gradeBlock.grade_label }}</span>
					<h3 class="block-title mb-0 fs-sm fw-semibold">
						{{ gradeBlock.groups.length }} grupos · {{ gradeBlock.specialties.length }} materias
					</h3>
				</div>
				<div class="d-flex align-items-center gap-2">
					<span class="text-muted fs-sm">
						{{ gradeFilled }} / {{ gradeTotal }} asignadas
					</span>
					<div class="progress flex-grow-1" style="min-width: 120px; max-width: 180px; height: 6px">
						<div
							class="progress-bar"
							:class="gradePercent === 100 ? 'bg-success' : 'bg-primary'"
							:style="{ width: `${gradePercent}%` }"
						></div>
					</div>
				</div>
			</div>
		</template>

		<p v-if="subjectSummary" class="text-muted fs-sm px-3 pt-2 mb-0">
			{{ subjectSummary }}
		</p>

		<div class="subject-assignment-table-wrap">
			<table class="table table-bordered table-sm subject-assignment-table mb-0">
				<thead>
					<tr>
						<th class="sticky-col text-uppercase fs-xs">Grupos</th>
						<th
							v-for="specialty in gradeBlock.specialties"
							:key="specialty.id"
							class="text-uppercase fs-xs text-center specialty-col"
							:title="`${specialty.description} (${specialty.hours_per_week}h/sem)`"
						>
							<span class="specialty-head">{{ specialtyColumnLabel(specialty) }}</span>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="group in gradeBlock.groups" :key="group.id">
						<th class="sticky-col fw-semibold text-nowrap group-col">
							{{ group.group_label || group.name }}
						</th>
						<td
							v-for="specialty in gradeBlock.specialties"
							:key="`${group.id}-${specialty.id}`"
							class="assignment-cell"
							:class="{ 'assignment-cell-filled': isFilled(group.id, specialty.id) }"
						>
							<div class="assignment-cell-inner">
								<label class="assignment-cell-label">Docente</label>
								<div class="assignment-cell-controls">
									<TeacherSelectWithTooltip
										class="assignment-cell-select"
										:model-value="cellValue(group.id, specialty.id)"
										:teachers="teachers"
										compact
										placeholder="-- Seleccionar --"
										@update:model-value="(value) => emitChange(group.id, specialty.id, value)"
									/>
									<button
										v-if="isFilled(group.id, specialty.id)"
										type="button"
										class="btn btn-sm btn-alt-secondary assignment-cell-clear"
										title="Quitar docente"
										aria-label="Quitar docente"
										@click="clearCell(group.id, specialty.id)"
									>
										<i class="fa-solid fa-xmark"></i>
									</button>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</BaseBlock>
</template>

<script setup>
import { computed } from 'vue';
import TeacherSelectWithTooltip from '@/components/TeacherSelectWithTooltip.vue';
import { cellKey, specialtyColumnLabel } from '@/utils/subjectAssignment';

const props = defineProps({
	gradeBlock: {
		type: Object,
		required: true,
	},
	teachers: {
		type: Array,
		default: () => [],
	},
	cellValues: {
		type: Object,
		default: () => ({}),
	},
});

const emit = defineEmits(['cell-change']);

function cellValue(groupId, specialtyId) {
	return props.cellValues[cellKey(groupId, specialtyId)] ?? '';
}

function isFilled(groupId, specialtyId) {
	return Boolean(cellValue(groupId, specialtyId));
}

function emitChange(groupId, specialtyId, value) {
	emit('cell-change', { groupId, specialtyId, teacherId: value || null });
}

function clearCell(groupId, specialtyId) {
	emitChange(groupId, specialtyId, null);
}

const subjectSummary = computed(() => {
	const labels = props.gradeBlock.specialties
		.map((specialty) => specialtyColumnLabel(specialty))
		.filter(Boolean);

	if (!labels.length) {
		return '';
	}

	return `${labels.length} materias: ${labels.join(', ')}`;
});

const gradeTotal = computed(
	() => props.gradeBlock.groups.length * props.gradeBlock.specialties.length
);

const gradeFilled = computed(() => {
	let count = 0;
	for (const group of props.gradeBlock.groups) {
		for (const specialty of props.gradeBlock.specialties) {
			if (isFilled(group.id, specialty.id)) {
				count++;
			}
		}
	}
	return count;
});

const gradePercent = computed(() =>
	gradeTotal.value > 0 ? Math.round((gradeFilled.value / gradeTotal.value) * 100) : 0
);
</script>

<style scoped>
:deep(.subject-assignment-grade-block.block) {
	overflow: visible;
}

:deep(.subject-assignment-grade-block .block-content) {
	overflow: visible;
}

.subject-assignment-table-wrap {
	width: 100%;
	overflow: auto;
	max-height: min(70vh, 560px);
	-webkit-overflow-scrolling: touch;
	border-radius: 0 0 0.75rem 0.75rem;
}

.subject-assignment-table {
	width: max-content;
	min-width: 100%;
	table-layout: auto;
}

.subject-assignment-table thead th {
	position: sticky;
	top: 0;
	z-index: 2;
	background: var(--bs-body-bg, #fff);
	vertical-align: bottom;
}

.sticky-col {
	position: sticky;
	left: 0;
	z-index: 3;
	background: var(--bs-body-bg, #fff);
	min-width: 6.5rem;
	box-shadow: 2px 0 4px rgba(10, 37, 64, 0.06);
}

.group-col {
	z-index: 4;
	min-width: 6.5rem;
	max-width: 10rem;
	white-space: normal;
	word-break: break-word;
}

.specialty-col {
	min-width: 11rem;
	width: 11rem;
}

.specialty-head {
	display: block;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.assignment-cell {
	padding: 0.375rem !important;
	vertical-align: middle;
	min-width: 11rem;
	width: 11rem;
	background: var(--bs-tertiary-bg, #f8f9fa);
	overflow: visible;
}

.assignment-cell-filled {
	background: rgba(var(--bs-success-rgb, 25, 135, 84), 0.08);
}

.assignment-cell-inner {
	display: flex;
	flex-direction: column;
	gap: 0.2rem;
	min-width: 0;
}

.assignment-cell-label {
	font-size: 0.6875rem;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.02em;
	color: var(--bs-secondary-color, #6c757d);
	margin: 0;
	line-height: 1.2;
}

.assignment-cell-controls {
	display: flex;
	align-items: stretch;
	gap: 0.25rem;
	min-width: 0;
}

.assignment-cell-select {
	flex: 1 1 auto;
	min-width: 0;
}

.assignment-cell-clear {
	flex: 0 0 auto;
	align-self: stretch;
	padding-inline: 0.5rem;
	line-height: 1;
}
</style>

<template>
	<form @submit.prevent="$emit('submit')" class="row g-3">
		<div class="col-md-6">
			<label class="form-label fw-semibold">Docente</label>
			<TeacherSelectWithTooltip
				v-model="form.teacher_id"
				:teachers="teachers"
				:disabled="disabled"
			/>
		</div>
		<div class="col-md-6">
			<label class="form-label fw-semibold">Materia</label>
			<select v-model="form.specialty_id" class="form-select" required :disabled="disabled">
				<option disabled value="">— Seleccionar —</option>
				<option v-for="s in specialties" :key="s.id" :value="s.id">
					{{ s.description }}
				</option>
			</select>
		</div>
		<div class="col-md-6">
			<label class="form-label fw-semibold">Grupo</label>
			<select
				v-model="form.academic_group_id"
				class="form-select"
				required
				:disabled="disabled"
			>
				<option disabled value="">— Seleccionar —</option>
				<option v-for="g in groupsForShift" :key="g.id" :value="g.id">
					{{ g.name || g.group_label || 'Grupo' }} — {{ shiftLabel(g.shift) }}
				</option>
			</select>
			<div v-if="form.shift && !groupsForShift.length" class="form-text text-warning">
				No hay grupos registrados para este turno.
			</div>
		</div>
		<div class="col-md-6">
			<label class="form-label fw-semibold">Turno</label>
			<select v-model="form.shift" class="form-select" required :disabled="disabled">
				<option value="morning">Matutino</option>
				<option value="afternoon">Vespertino</option>
			</select>
		</div>
		<div class="col-md-6">
			<label class="form-label fw-semibold">Día</label>
			<select v-model="form.day_of_week" class="form-select" required :disabled="disabled">
				<option v-for="d in dayOptions" :key="d.value" :value="d.value">
					{{ d.label }}
				</option>
			</select>
		</div>
		<div class="col-md-6">
			<label class="form-label fw-semibold">Hora (franja)</label>
			<select v-model="form.time_slot" class="form-select" required :disabled="disabled">
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
				@click="$emit('reset')"
				:disabled="disabled"
			>
				<i class="fa-solid fa-eraser me-1"></i>
				Limpiar
			</button>
			<button type="submit" class="btn btn-primary" :disabled="disabled">
				<i class="fa fa-cog fa-spin me-1" v-if="disabled"></i>
				<i class="fa-solid fa-floppy-disk me-1" v-else></i>
				{{ submitLabel }}
			</button>
		</div>
	</form>
</template>

<script setup>
import TeacherSelectWithTooltip from '@/components/TeacherSelectWithTooltip.vue';

const props = defineProps({
	form: {
		type: Object,
		required: true,
	},
	teachers: {
		type: Array,
		default: () => [],
	},
	specialties: {
		type: Array,
		default: () => [],
	},
	academicGroups: {
		type: Array,
		default: () => [],
	},
	dayOptions: {
		type: Array,
		default: () => [],
	},
	morningSlots: {
		type: Array,
		default: () => [],
	},
	eveningSlots: {
		type: Array,
		default: () => [],
	},
	disabled: {
		type: Boolean,
		default: false,
	},
	submitLabel: {
		type: String,
		default: 'Guardar asignación',
	},
});

defineEmits(['submit', 'reset']);

const slotOptions = computed(() =>
	props.form.shift === 'morning' ? props.morningSlots : props.eveningSlots
);

const groupsForShift = computed(() =>
	props.academicGroups.filter((g) => g.shift === props.form.shift)
);

function shiftLabel(shift) {
	return shift === 'afternoon' ? 'Vespertino' : 'Matutino';
}

watch(
	() => props.form.shift,
	() => {
		props.form.academic_group_id = '';
		const slots = props.form.shift === 'morning' ? props.morningSlots : props.eveningSlots;
		if (!slots.includes(props.form.time_slot)) {
			props.form.time_slot = slots[0] || '';
		}
	}
);
</script>

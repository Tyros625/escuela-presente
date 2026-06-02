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

			<select v-model="form.specialty_id" class="form-select" required :disabled="disabled || !form.teacher_id">

				<option disabled value="">— Seleccionar —</option>

				<option v-for="s in filteredSpecialties" :key="s.id" :value="s.id">

					{{ s.description }}

				</option>

			</select>

			<div v-if="form.teacher_id && !filteredSpecialties.length" class="form-text text-warning">

				Este docente no tiene materias asignadas en Asignación de materias.

			</div>

		</div>

		<div class="col-md-6">

			<label class="form-label fw-semibold">Grupo</label>

			<select

				v-model="form.academic_group_id"

				class="form-select"

				required

				:disabled="disabled || groupFieldLocked || !form.specialty_id"

			>

				<option disabled value="">— Seleccionar —</option>

				<option v-for="g in filteredGroups" :key="g.id" :value="g.id">

					{{ g.name || g.group_label || 'Grupo' }} — {{ shiftLabel(g.shift) }}

				</option>

			</select>

			<div v-if="isServicioSelected" class="form-text text-muted">

				La materia SERVICIO se asigna automáticamente al grupo SIN GRUPO.

			</div>

			<div v-else-if="form.specialty_id && form.shift && !filteredGroups.length" class="form-text text-warning">

				No hay grupos asignados a este docente y materia en el período seleccionado.

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

import { isServicioSpecialty, isSinGrupoGroup } from '@/utils/teachingSchedule';



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

	subjectLinks: {

		type: Array,

		default: () => [],

	},

	sinGrupoGroupIds: {

		type: Object,

		default: () => ({}),

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



const selectedSpecialty = computed(() =>

	props.specialties.find((s) => Number(s.id) === Number(props.form.specialty_id)) || null

);



const isServicioSelected = computed(() => isServicioSpecialty(selectedSpecialty.value));



const groupFieldLocked = computed(() => isServicioSelected.value);



const teacherSubjectLinks = computed(() => {

	const teacherId = Number(props.form.teacher_id);

	if (!teacherId) {

		return [];

	}



	return props.subjectLinks.filter((link) => Number(link.teacher_id) === teacherId);

});



const filteredSpecialties = computed(() => {

	if (!props.form.teacher_id) {

		return props.specialties;

	}



	const specialtyIds = new Set(teacherSubjectLinks.value.map((link) => Number(link.specialty_id)));

	const currentSpecialtyId = Number(props.form.specialty_id);

	if (currentSpecialtyId) {

		specialtyIds.add(currentSpecialtyId);

	}



	return props.specialties.filter((specialty) => specialtyIds.has(Number(specialty.id)));

});



const filteredGroups = computed(() => {

	const shift = props.form.shift;

	let groups = props.academicGroups.filter((group) => group.shift === shift);



	if (isServicioSelected.value) {

		const sinGrupoId = props.sinGrupoGroupIds[shift];

		if (sinGrupoId) {

			return groups.filter((group) => Number(group.id) === Number(sinGrupoId));

		}



		return groups.filter((group) => isSinGrupoGroup(group));

	}



	if (props.form.teacher_id && props.form.specialty_id) {

		const allowedIds = new Set(

			teacherSubjectLinks.value

				.filter((link) => Number(link.specialty_id) === Number(props.form.specialty_id))

				.map((link) => Number(link.academic_group_id))

		);



		groups = groups.filter((group) => allowedIds.has(Number(group.id)));

	} else if (props.form.teacher_id) {

		const allowedIds = new Set(teacherSubjectLinks.value.map((link) => Number(link.academic_group_id)));

		groups = groups.filter((group) => allowedIds.has(Number(group.id)));

	}



	const currentGroupId = Number(props.form.academic_group_id);

	if (currentGroupId && !groups.some((group) => Number(group.id) === currentGroupId)) {

		const currentGroup = props.academicGroups.find((group) => Number(group.id) === currentGroupId);

		if (currentGroup) {

			groups = [...groups, currentGroup];

		}

	}



	return groups;

});



function shiftLabel(shift) {

	return shift === 'afternoon' ? 'Vespertino' : 'Matutino';

}



function applySinGrupoGroup() {

	if (!isServicioSelected.value) {

		return;

	}



	const sinGrupoId = props.sinGrupoGroupIds[props.form.shift];

	if (sinGrupoId) {

		props.form.academic_group_id = sinGrupoId;

		return;

	}



	const fallback = props.academicGroups.find(

		(group) => group.shift === props.form.shift && isSinGrupoGroup(group)

	);



	if (fallback) {

		props.form.academic_group_id = fallback.id;

	}

}



function autoSelectSingleGroup() {

	if (isServicioSelected.value || filteredGroups.value.length !== 1) {

		return;

	}



	props.form.academic_group_id = filteredGroups.value[0].id;

}



watch(

	() => props.form.teacher_id,

	() => {

		props.form.specialty_id = '';

		props.form.academic_group_id = '';

	}

);



watch(

	() => props.form.specialty_id,

	() => {

		if (isServicioSelected.value) {

			applySinGrupoGroup();

			return;

		}



		props.form.academic_group_id = '';

		autoSelectSingleGroup();

	}

);



watch(

	() => props.form.shift,

	() => {

		if (isServicioSelected.value) {

			applySinGrupoGroup();

			return;

		}



		const currentGroupId = Number(props.form.academic_group_id);

		const stillValid = filteredGroups.value.some((group) => Number(group.id) === currentGroupId);



		if (!stillValid) {

			props.form.academic_group_id = '';

			autoSelectSingleGroup();

		}



		const slots = props.form.shift === 'morning' ? props.morningSlots : props.eveningSlots;

		if (!slots.includes(props.form.time_slot)) {

			props.form.time_slot = slots[0] || '';

		}

	}

);



watch(filteredGroups, () => {

	if (isServicioSelected.value) {

		applySinGrupoGroup();

		return;

	}



	autoSelectSingleGroup();

});

</script>



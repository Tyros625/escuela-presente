<template>
	<div class="teacher-select-wrapper" ref="wrapperRef">
		<button
			type="button"
			class="form-select text-start d-flex align-items-center justify-content-between"
			:class="{ 'text-muted': !selectedTeacher }"
			:disabled="disabled"
			@click="toggleOpen"
			@keydown.escape="closeMenu"
		>
			<span class="text-truncate">
				{{ selectedTeacher ? teacherLabel(selectedTeacher) : placeholder }}
			</span>
			<i class="fa-solid fa-chevron-down fs-xs opacity-50 ms-2"></i>
		</button>

		<div v-if="isOpen && !disabled" class="teacher-select-menu shadow-sm">
			<div
				v-for="teacher in teachers"
				:key="teacher.id"
				class="teacher-select-option"
				:class="{ active: Number(modelValue) === Number(teacher.id) }"
				@mouseenter="onHover($event, teacher)"
				@mouseleave="clearHover"
				@click="selectTeacher(teacher)"
			>
				{{ teacherLabel(teacher) }}
			</div>
			<div v-if="!teachers.length" class="teacher-select-empty text-muted">
				No hay docentes disponibles
			</div>
		</div>

		<div
			v-if="tooltip.visible && tooltip.text"
			class="teacher-select-tooltip shadow-sm"
			:style="{ top: `${tooltip.top}px`, left: `${tooltip.left}px` }"
		>
			{{ tooltip.text }}
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	modelValue: {
		type: [String, Number],
		default: '',
	},
	teachers: {
		type: Array,
		default: () => [],
	},
	disabled: {
		type: Boolean,
		default: false,
	},
	placeholder: {
		type: String,
		default: '— Seleccionar —',
	},
});

const emit = defineEmits(['update:modelValue']);

const wrapperRef = ref(null);
const isOpen = ref(false);
const tooltip = reactive({
	visible: false,
	text: '',
	top: 0,
	left: 0,
});

const selectedTeacher = computed(() =>
	props.teachers.find((t) => Number(t.id) === Number(props.modelValue)) || null
);

function teacherLabel(teacher) {
	return teacher.display_name || formatTeacherName(teacher);
}

function formatTeacherName(t) {
	const ln = [t.last_name_father, t.last_name_mother].filter(Boolean).join(' ');
	return ln ? `${ln}, ${t.name || ''}` : t.name || '';
}

function toggleOpen() {
	if (props.disabled) return;
	isOpen.value = !isOpen.value;
	if (!isOpen.value) clearHover();
}

function closeMenu() {
	isOpen.value = false;
	clearHover();
}

function selectTeacher(teacher) {
	emit('update:modelValue', teacher.id);
	closeMenu();
}

function onHover(event, teacher) {
	const specialty = teacher.specialization || teacher.subject;
	if (!specialty) {
		clearHover();
		return;
	}

	const rect = event.currentTarget.getBoundingClientRect();
	const wrapperRect = wrapperRef.value?.getBoundingClientRect() || { top: 0, left: 0 };

	tooltip.text = specialty;
	tooltip.top = rect.top - wrapperRect.top + rect.height / 2;
	tooltip.left = rect.right - wrapperRect.left + 8;
	tooltip.visible = true;
}

function clearHover() {
	tooltip.visible = false;
	tooltip.text = '';
}

function onDocumentClick(event) {
	if (!wrapperRef.value?.contains(event.target)) {
		closeMenu();
	}
}

onMounted(() => {
	document.addEventListener('click', onDocumentClick);
});

onBeforeUnmount(() => {
	document.removeEventListener('click', onDocumentClick);
});
</script>

<style scoped>
.teacher-select-wrapper {
	position: relative;
}

.teacher-select-menu {
	position: absolute;
	z-index: 1050;
	top: calc(100% + 4px);
	left: 0;
	right: 0;
	max-height: 260px;
	overflow-y: auto;
	background: var(--bs-body-bg, #fff);
	border: 1px solid var(--bs-border-color, #dfe3ea);
	border-radius: 0.375rem;
}

.teacher-select-option {
	padding: 0.5rem 0.75rem;
	cursor: pointer;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.teacher-select-option:hover,
.teacher-select-option.active {
	background: var(--bs-primary, #0d6efd);
	color: #fff;
}

.teacher-select-empty {
	padding: 0.75rem;
	font-size: 0.875rem;
}

.teacher-select-tooltip {
	position: absolute;
	z-index: 1060;
	transform: translateY(-50%);
	background: #fff;
	border: 1px solid var(--bs-border-color, #dfe3ea);
	border-radius: 0.375rem;
	padding: 0.35rem 0.65rem;
	font-size: 0.8125rem;
	font-weight: 600;
	color: var(--bs-body-color, #1f2937);
	white-space: nowrap;
	pointer-events: none;
}
</style>

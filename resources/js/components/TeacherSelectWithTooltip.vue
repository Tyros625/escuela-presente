<template>
	<div class="teacher-select-wrapper" ref="wrapperRef">
		<button
			type="button"
			class="teacher-select-trigger text-start d-flex align-items-center justify-content-between w-100"
			:class="{ 'text-muted': !selectedTeacher, 'teacher-select-compact': compact }"
			:disabled="disabled"
			@click="toggleOpen"
			@keydown.escape="closeMenu"
		>
			<span class="text-truncate">
				{{ selectedTeacher ? teacherLabel(selectedTeacher) : placeholder }}
			</span>
			<i class="fa-solid fa-chevron-down teacher-select-chevron" aria-hidden="true"></i>
		</button>

		<Teleport to="body">
			<div
				v-if="isOpen && !disabled"
				ref="menuRef"
				class="teacher-select-menu shadow"
				:style="menuStyle"
			>
				<button
					type="button"
					class="teacher-select-option teacher-select-clear"
					:class="{ active: !modelValue }"
					@click="clearSelection"
				>
					— Sin asignar —
				</button>
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
		</Teleport>

		<Teleport to="body">
			<div
				v-if="tooltip.visible && tooltip.text"
				class="teacher-select-tooltip shadow-sm"
				:style="tooltipStyle"
			>
				{{ tooltip.text }}
			</div>
		</Teleport>
	</div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref } from 'vue';

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
	compact: {
		type: Boolean,
		default: false,
	},
});

const emit = defineEmits(['update:modelValue']);

const wrapperRef = ref(null);
const menuRef = ref(null);
const isOpen = ref(false);
const menuStyle = ref({});
const tooltip = reactive({
	visible: false,
	text: '',
	top: 0,
	left: 0,
});

const tooltipStyle = computed(() => ({
	position: 'fixed',
	top: `${tooltip.top}px`,
	left: `${tooltip.left}px`,
	zIndex: 1070,
}));

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

function positionMenu() {
	const trigger = wrapperRef.value?.querySelector('.teacher-select-trigger');
	if (!trigger) {
		return;
	}

	const rect = trigger.getBoundingClientRect();
	const viewportPadding = 8;
	const menuWidth = Math.max(rect.width, 280);
	let left = rect.left;

	if (left + menuWidth > window.innerWidth - viewportPadding) {
		left = window.innerWidth - menuWidth - viewportPadding;
	}
	left = Math.max(viewportPadding, left);

	const spaceBelow = window.innerHeight - rect.bottom;
	const spaceAbove = rect.top;
	const openUp = spaceBelow < 220 && spaceAbove > spaceBelow;
	const maxHeight = Math.min(320, openUp ? spaceAbove - 12 : spaceBelow - 12);

	menuStyle.value = {
		position: 'fixed',
		top: openUp ? `${rect.top - maxHeight - 4}px` : `${rect.bottom + 4}px`,
		left: `${left}px`,
		width: `${menuWidth}px`,
		minWidth: `${menuWidth}px`,
		maxHeight: `${Math.max(160, maxHeight)}px`,
		zIndex: 1060,
	};
}

function toggleOpen() {
	if (props.disabled) {
		return;
	}
	isOpen.value = !isOpen.value;
	if (isOpen.value) {
		nextTick(() => positionMenu());
	} else {
		clearHover();
	}
}

function closeMenu() {
	isOpen.value = false;
	clearHover();
}

function selectTeacher(teacher) {
	emit('update:modelValue', teacher.id);
	closeMenu();
}

function clearSelection() {
	emit('update:modelValue', '');
	closeMenu();
}

function onHover(event, teacher) {
	const specialty = teacher.specialization || teacher.subject;
	if (!specialty) {
		clearHover();
		return;
	}

	const rect = event.currentTarget.getBoundingClientRect();
	tooltip.text = specialty;
	tooltip.top = rect.top + rect.height / 2;
	tooltip.left = rect.right + 10;
	tooltip.visible = true;
}

function clearHover() {
	tooltip.visible = false;
	tooltip.text = '';
}

function onDocumentClick(event) {
	const menu = menuRef.value;
	if (
		!wrapperRef.value?.contains(event.target) &&
		!menu?.contains(event.target)
	) {
		closeMenu();
	}
}

function onReposition() {
	if (isOpen.value) {
		positionMenu();
	}
}

onMounted(() => {
	document.addEventListener('click', onDocumentClick);
	window.addEventListener('resize', onReposition);
	window.addEventListener('scroll', onReposition, true);
});

onBeforeUnmount(() => {
	document.removeEventListener('click', onDocumentClick);
	window.removeEventListener('resize', onReposition);
	window.removeEventListener('scroll', onReposition, true);
});
</script>

<style scoped>
.teacher-select-wrapper {
	position: relative;
	width: 100%;
	min-width: 0;
}

.teacher-select-trigger {
  border: 1px solid var(--bs-border-color, #d8dee9);
  border-radius: 0.5rem;
  background-color: var(--bs-body-bg, #fff);
  color: var(--bs-body-color, #0a2540);
	font-size: 0.875rem;
	font-weight: 500;
	padding: 0.4375rem 0.625rem;
	min-height: 2.25rem;
	box-shadow: 0 1px 2px rgba(10, 37, 64, 0.03);
	transition: border-color 0.15s ease, box-shadow 0.15s ease;

	&:hover:not(:disabled),
	&:focus:not(:disabled) {
		border-color: #635bff;
		box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.15);
	}

	&:disabled {
		opacity: 0.65;
		cursor: not-allowed;
	}
}

.teacher-select-compact {
	font-size: 0.8125rem;
	padding: 0.35rem 0.5rem;
	min-height: 2rem;
}

.teacher-select-chevron {
	flex-shrink: 0;
	font-size: 0.6875rem;
	opacity: 0.55;
	margin-left: 0.375rem;
}

.teacher-select-clear {
	width: 100%;
	border: 0;
	background: transparent;
	text-align: left;
	color: var(--bs-secondary-color, #6c757d);
}

.teacher-select-menu {
  overflow-y: auto;
  background-color: var(--bs-body-bg, #fff);
  border: 1px solid var(--bs-border-color, #dfe3ea);
	border-radius: 0.5rem;
}

.teacher-select-option {
	padding: 0.5625rem 0.875rem;
	cursor: pointer;
	white-space: normal;
	word-break: break-word;
	line-height: 1.35;
}

.teacher-select-option:hover,
.teacher-select-option.active {
	background: #635bff;
	color: #fff;
}

.teacher-select-empty {
	padding: 0.75rem 0.875rem;
	font-size: 0.875rem;
}

.teacher-select-tooltip {
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
